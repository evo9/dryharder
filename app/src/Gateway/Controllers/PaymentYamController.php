<?php

namespace Dryharder\Gateway\Controllers;

use Config;
use Controller;
use Dryharder\Agbis\Api;
use Dryharder\Agbis\ApiException;
use Dryharder\Components\Payment\CloudPaymentException;
use Dryharder\Components\Reporter;
use Dryharder\Gateway\Models\PaymentCloud;
use Input;
use Redirect;

class PaymentYamController extends Controller
{

    /**
     * сюда от платежного шлюза приходит запрос на check
     *
     * @throws CloudPaymentException
     */
    public function check()
    {
        $this->parseRequest();
        $check = $this->checkParameters();
        if ($check !== true) {
            return $check;
        }

        $input = Input::all();
        Reporter::payExternalCheck(Input::get('customerNumber'), Input::get('invoiceId'), Input::get('orderSumAmount'), $input);

        $check = $this->checkOrder();
        if (true !== $check) {
            return $check;
        }

        return $this->processCheckRequest();

    }

    /**
     * сюда от платежного шлюза приходит запрос на pay
     *
     * @throws CloudPaymentException
     */
    public function pay()
    {

        $this->parseRequest();
        $check = $this->checkParameters();
        if ($check !== true) {
            return $check;
        }

        $input = Input::all();
        Reporter::payExternalPay(Input::get('customerNumber'), Input::get('invoiceId'), Input::get('orderSumAmount'), $input);

        return $this->processPayRequest();

    }

    /**
     * сюда от платежного шлюза приходит запрос с информацией об ошибке платежа
     * пометим транзакцию fail, чтобы не мешала выполнять повторные платежи
     *
     * @throws CloudPaymentException
     */
    public function fail()
    {

        $this->parseRequest();
        $input = Input::all();
        Reporter::payExternalFail(Input::get('AccountId'), Input::get('InvoiceId'), Input::get('Amount'), $input);

        return $this->processPayFail();

    }


    /**
     * процессинг по запросу check
     * создает запись в транзакциях на оплату
     */
    private function processCheckRequest()
    {
        $params = $this->parameters();

        // вдруг уже был чек с этими параметрами
        $pay = PaymentCloud::whereCustomerId($params['customer_id'])
            ->whereOrderId($params['order_id'])
            ->wherePaymentId($params['payment_id'])
            ->whereWaiting(1)
            ->first();

        if (!$pay) {
            $pay = new PaymentCloud();
        }
        $pay->unguard();
        $pay->fill($params);
        $pay->card_type = PaymentCloud::getPayCardType4Yam(Input::get('paymentType'));
        $pay->card_pan = '100000...0001';
        $pay->card_holder = 'YANDEX MONEY';
        $pay->failed = 0;
        $pay->save();

        if (!$pay) {
            Reporter::payTransactionFail($params['customer_id'], $params['order_id'], $params['payment_id']);

            return $this->responseError('Ошибка сохранения данных об оплате заказа', 100);
        }

        Reporter::payTransactionCreated($params['customer_id'], $params['order_id'], $params['payment_id'], $pay->id);

        return $this->responseSuccess();

    }

    /**
     * процессинг по запросу pay
     * подтверждает транзакцию на оплату по ранее созданной записи
     */
    private function processPayRequest()
    {
        $params = $this->parameters();

        // может быть уже была оплата, сразу ответим "ок"
        $pay = PaymentCloud::whereCustomerId($params['customer_id'])
            ->whereOrderId($params['order_id'])
            ->wherePaymentId($params['payment_id'])
            ->whereWaiting(0)
            ->first();

        if ($pay) {
            return $this->responseSuccess();
        }

        $check = $this->checkOrder();
        if (true !== $check) {
            return $check;
        }

        // такая транзакция уже должна быть в нашей базе, и не оплаченная
        $pay = PaymentCloud::whereCustomerId($params['customer_id'])
            ->whereOrderId($params['order_id'])
            ->wherePaymentId($params['payment_id'])
            ->whereWaiting(1)
            ->first();

        // если ее нет, значит ничего не делаем
        if (!$pay) {
            Reporter::payTransactionLost($params['customer_id'], $params['order_id'], $params['payment_id']);

            return $this->responseError('Ошибка статуса платежа', 200);
        }

        Reporter::payTransactionFound($params['customer_id'], $params['order_id'], $params['payment_id'], $pay->id);

        // обновляем транзакцию как оплаченную
        $pay->request = $params['request'];
        $pay->paidYam();

        Reporter::payTransactionPaid($params['customer_id'], $params['order_id'], $params['payment_id'], $pay->id);

        return $this->responseSuccess();

    }

    /**
     * процессинг по запросу fail
     * помечает заказ ожидающую транзакцию как не успешную
     */
    private function processPayFail()
    {
        $params = $this->parameters();

        $pay = PaymentCloud::whereCustomerId($params['customer_id'])
            ->whereOrderId($params['order_id'])
            ->wherePaymentId($params['payment_id'])
            ->whereWaiting(1)
            ->first();

        if ($pay) {
            $pay->failed = 1;
            $pay->request = $params['request'];
            $pay->save();
        }

        return Redirect::to('/account')->withErrors([
            'payment' => trans('main.Order payment is failed'),
        ]);

    }

    /**
     * проверяет, что заказ присутствует в системе Агбис
     */
    private function checkOrder()
    {

        $params = $this->parameters();

        try {

            $api = new Api();
            $api->IsGoodOrder((int)$params['order_id'], (int)$params['customer_id']);
            Reporter::payOrderFound($params['customer_id'], $params['order_id']);

        } catch (ApiException $e) {

            Reporter::payOrderLost($params['customer_id'], $params['order_id']);

            if ($e->getCode() == 400) {
                return $this->responseError('Ошибка оплаты заказа', 200);
            }

            return $this->responseError('Ошибка оплаты заказа', 200);

        }

        return true;

    }

    /**
     * компилирует параметры запроса в наш формат
     *
     * @return array
     */
    private function parameters()
    {

        $fields = [
            'orderNumber'          => 'order_id',
            'customerNumber'       => 'customer_id',
            'orderSumAmount'       => 'amount',
            'orderCreatedDatetime' => 'external_at',
            'invoiceId'            => 'payment_id',
        ];

        $params = [];
        foreach ($fields as $from => $to) {
            $params[$to] = trim(Input::get($from));
        }

        // все данные запроса
        $params['request'] = json_encode(Input::all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $params;

    }

    /**
     * проверяет обязательные параметры запроса
     */
    private function checkParameters()
    {

        $params = $this->parameters();

        Reporter::payExternalParameters($params);

        if (
            empty($params['order_id']) ||
            empty($params['customer_id'])
        ) {
            Reporter::payExternalParametersFail($params);

            return $this->responseError('Ошибка параметров запроса', 200);
        }

        $params4Hash = [
            'action'                  => Input::get('action'),
            'orderSumAmount'          => Input::get('orderSumAmount'),
            'orderSumCurrencyPaycash' => Input::get('orderSumCurrencyPaycash'),
            'orderSumBankPaycash'     => Input::get('orderSumBankPaycash'),
            'shopId'                  => Input::get('shopId'),
            'invoiceId'               => Input::get('invoiceId'),
            'customerNumber'          => Input::get('customerNumber'),
            'shopPassword'            => Config::get('cloud.yam.secret'),
        ];
        $str2Hash = implode(';', $params4Hash);
        $hash = strtoupper(md5($str2Hash));
        \Log::debug('параметры для подписи', [$params4Hash]);
        \Log::debug('строка для подписи', [$str2Hash]);
        \Log::debug('подпись', [$hash]);
        \Log::debug('подпись запроса', [Input::get('md5')]);
        if ($hash !== Input::get('md5')) {
            return $this->responseError('Ошибка авторизации (подпись)', 1);
        }

        return true;


    }

    /**
     * ответ ошибка на check
     *
     * @param string $errorMessage
     * @param int $code
     *
     * @return \Illuminate\Http\Response
     */
    private function responseError($errorMessage, $code = 100)
    {
        $method = Input::get('action') == 'checkOrder' ? 'checkOrder' : 'paymentAviso';
        $params = $this->parameters();
        Reporter::payResponseError($params['customer_id'], $params['order_id'], $params['payment_id'], $errorMessage);

        $dt = date('Y-m-d\TH:i:s+04:00');
        $response = '<?xml version="1.0" encoding="UTF-8"?><' . $method . 'Response performedDatetime="' . $dt . '" code="' . $code . '" invoiceId="' . Input::get('invoiceId') . '" shopId="' . Config::get('cloud.yam.shopId') . '" message="' . $errorMessage . '" techMessage="' . $errorMessage . '"/>';
        \Log::debug('response', [$response]);

        return \Response::make($response, 200, [
            'Content-Type' => 'application/xml',
        ]);

    }

    /**
     * ответ успешно на check
     */
    private function responseSuccess()
    {
        $method = Input::get('action') == 'checkOrder' ? 'checkOrder' : 'paymentAviso';
        $params = $this->parameters();
        Reporter::payResponseSuccess($params['customer_id'], $params['order_id'], $params['payment_id']);

        $dt = date('Y-m-d\TH:i:s+04:00');
        $response = '<?xml version="1.0" encoding="UTF-8"?><' . $method . 'Response performedDatetime="' . $dt . '" code="0" invoiceId="' . Input::get('invoiceId') . '" shopId="' . Config::get('cloud.yam.shopId') . '"/>';
        \Log::debug('response', [$response]);

        return \Response::make($response, 200, [
            'Content-Type' => 'application/xml',
        ]);

    }

    private function parseRequest()
    {

        $data = file('php://input');
        $parsed = [];
        if (!empty($data[0])) {
            parse_str($data[0], $parsed);
            Input::merge($parsed);
        }

    }

}