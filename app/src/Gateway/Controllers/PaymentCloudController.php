<?php

namespace Dryharder\Gateway\Controllers;

use Config;
use Controller;
use DB;
use Dryharder\Agbis\Api;
use Dryharder\Agbis\ApiException;
use Dryharder\Components\Customer;
use Dryharder\Components\Payment\CloudPaymentException;
use Dryharder\Components\Reporter;
use Dryharder\Gateway\Components\GenerateAgentXML;
use Dryharder\Gateway\Models\PaymentCloud;
use Dryharder\Gateway\Models\CloudPaymentsCard;
use Input;
use Log;
use Response;

class PaymentCloudController extends Controller
{
    private $params = [];

    /**
     * сюда от платежного шлюза приходит запрос на check
     *
     * @throws CloudPaymentException
     */
    public function check()
    {
        $this->checkParameters();

        $input = Input::all();
        Reporter::payExternalCheck($input['AccountId'], $input['InvoiceId'], $input['Amount'], $input);

        //$this->filter();
        if ($this->params['order_id'] > '') {
            $this->checkOrder();
            $this->processCheckRequest();
        }
        else {
            $this->saveCard();
        }
    }

    /**
     * сюда от платежного шлюза приходит запрос на pay
     *
     * @throws CloudPaymentException
     */
    public function pay()
    {
        $this->checkParameters();

        $input = Input::all();
        Reporter::payExternalPay($input['AccountId'], $input['InvoiceId'], $input['Amount'], $input);

        $this->filter();
        $this->checkOrder();
        $this->processPayRequest();

    }

    /**
     * сюда от платежного шлюза приходит запрос с информацией об ошибке платежа
     * пометим транзакцию fail, чтобы не мешала выполнять повторные платежи
     *
     * @throws CloudPaymentException
     */
    public function fail()
    {
        $this->checkParameters();

        $input = Input::all();
        Reporter::payExternalFail($input['AccountId'], $input['InvoiceId'], $input['Amount'], $input);

        $this->filter();
        $this->processPayFail();

    }


    /**
     * поиск оплаченного (подтвержденного) заказа по id
     * метод для нашего фронтенда
     *
     * @param integer $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function waiting($id)
    {

        // ищем оплаченный заказ с указанным id
        $pay = PaymentCloud::whereOrderId($id)->payed()->first();

        if ($pay) {
            return $this->response([], '', 'Найдена оплаченная транзакция');
        }

        return $this->response([], 'Не найдена оплаченная транзакция', 'Не найдена оплаченная транзакция', 404);

    }


    /**
     * поиск всех оплаченных, и не выгруженных заказов для запроса от агента
     */
    public function export()
    {
        $this->filterAgent();
        $guid = $this->getAgentGuid();
        $this->processAgentExport($guid);
        $this->processAgentCommit($guid);

    }


    /**
     * процессинг по запросу check
     * создает запись в транзакциях на оплату
     */
    private function processCheckRequest()
    {
        // вдруг уже был чек с этими параметрами
        $pay = PaymentCloud::whereCustomerId($this->params['customer_id'])
            ->whereOrderId($this->params['order_id'])
            ->wherePaymentId($this->params['payment_id'])
            ->whereWaiting(1)
            ->first();

        if (!$pay) {
            $pay = new PaymentCloud();
        }
        $pay->unguard();
        $pay->fill($this->params);
        $pay->failed = 0;
        $pay->save();

        if (!$pay) {
            Reporter::payTransactionFail($this->params['customer_id'], $this->params['order_id'], $this->params['payment_id']);
            $this->responseError(13);
        }

        Reporter::payTransactionCreated($this->params['customer_id'], $this->params['order_id'], $this->params['payment_id'], $pay->id);
        $this->responseSuccess();

    }

    /**
     * процессинг по запросу pay
     * подтверждает транзакцию на оплату по ранее созданной записи
     */
    private function processPayRequest()
    {
        // может быть уже была оплата, сразу ответим "ок"
        $pay = PaymentCloud::whereCustomerId($this->params['customer_id'])
            ->whereOrderId($this->params['order_id'])
            ->wherePaymentId($this->params['payment_id'])
            ->whereWaiting(0)
            ->first();

        if($pay){
            $this->responseSuccess();
        }

        // такая транзакция уже должна быть в нашей базе, и не оплаченная
        $pay = PaymentCloud::whereCustomerId($this->params['customer_id'])
            ->whereOrderId($this->params['order_id'])
            ->wherePaymentId($this->params['payment_id'])
            ->whereWaiting(1)
            ->first();

        // если ее нет, значит ничего не делаем
        if (!$pay) {
            Reporter::payTransactionLost($this->params['customer_id'], $this->params['order_id'], $this->params['payment_id']);
            $this->responseError(13);
        }

        Reporter::payTransactionFound($this->params['customer_id'], $this->params['order_id'], $this->params['payment_id'], $pay->id);

        // обновляем транзакцию как оплаченную
        $pay->request = $this->params['request'];
        $pay->paid($this->params['token']);

        // удалить все токены, если клиент против хранения карты
        $saveCard = Customer::instance()->initByExternalId($this->params['customer_id'])->isSaveCard();
        if(!$saveCard){
            PaymentCloud::removeTokens($this->params['customer_id']);
        }

        Reporter::payTransactionPaid($this->params['customer_id'], $this->params['order_id'], $this->params['payment_id'], $pay->id);

        $this->responseSuccess();

    }

    /**
     * процессинг по запросу fail
     * помечает заказ ожидающую транзакцию как не успешную
     */
    private function processPayFail()
    {
        $pay = PaymentCloud::whereCustomerId($this->params['customer_id'])
            ->whereOrderId($this->params['order_id'])
            ->wherePaymentId($this->params['payment_id'])
            ->whereWaiting(1)
            ->first();

        if ($pay) {
            $pay->failed = 1;
            $pay->request = $this->params['request'];
            $pay->save();
        }

        $this->responseSuccess();

    }

    /**
     * проверяет, что заказ присутствует в системе Агбис
     */
    private function checkOrder()
    {

        try {

            $api = new Api();
            $api->IsGoodOrder($this->params['order_id'], $this->params['customer_id']);
            Reporter::payOrderFound($this->params['customer_id'], $this->params['order_id']);

        } catch (ApiException $e) {

            Reporter::payOrderLost($this->params['customer_id'], $this->params['order_id']);

            if ($e->getCode() == 400) {
                $this->responseError(10);
            }

            $this->responseError(13);

        }


    }

    /**
     * компилирует параметры запроса в наш формат
     *
     * @return array
     */
    private function parameters()
    {

        $fields = [
            'InvoiceId'     => 'order_id',
            'AccountId'     => 'customer_id',
            'TransactionId' => 'payment_id',
            'Amount'        => 'amount',
            'Name'          => 'card_holder',
            'Email'         => 'email',
            'DateTime'      => 'external_at',
            'IpAddress'     => 'ip',
            'CardType'      => 'card_type',
            'Token'         => 'token',
        ];

        $params = [];
        foreach ($fields as $from => $to) {
            $params[$to] = trim(Input::get($from));
        }

        // номер карты
        $first = Input::get('CardFirstSix');
        $last = Input::get('CardLastFour');
        if ($first && $last) {
            $params['card_pan'] = $first . '...' . $last;
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

        $this->params = $this->parameters();

        Reporter::payExternalParameters($this->params);

        if (
            /*empty($this->params['order_id']) ||*/
            empty($this->params['customer_id'])
        ) {
            Reporter::payExternalParametersFail($this->params);
            $this->responseError(13);
        }

    }

    /**
     * проверяет клиента по ip
     *
     * @throws CloudPaymentException
     */
    private function filter()
    {

        $ip = @$_SERVER['REMOTE_ADDR'];

        if (empty($ip)) {
            throw new CloudPaymentException('lost client ip address');
        }

        $allowIps = Config::get('payments.cloud.allowIp');

        if (
            !empty($allowIps) &&
            !in_array($ip, $allowIps) &&
            strpos($ip, '10.') !== 0 &&
            strpos($ip, '192.') !== 0
        ) {
            Reporter::payExternalDeny($ip, $allowIps);
            throw new CloudPaymentException('disallow ip address');
        }

        Reporter::payExternalAllow($ip);

    }

    /**
     * ответ ошибка
     *
     * @param $errorCode
     */
    private function responseError($errorCode)
    {

        $data = [
            'code' => $errorCode,
        ];

        $params = $this->parameters();
        Reporter::payResponseError($params['customer_id'], $params['order_id'], $params['payment_id'], $errorCode);

        Response::json($data)->send();
        die();

    }

    /**
     * ответ успешно
     */
    private function responseSuccess()
    {

        $data = [
            'code' => 0
        ];

        Reporter::payResponseSuccess($this->params['customer_id'], $this->params['payment_id'], $this->params['order_id']);

        Response::json($data)->send();
        die();

    }

    /**
     * Добавление карты. ответ успешно
     */
    private function responseAddCardSuccess()
    {

        $data = [
            'code' => 0,
            'addCard' => true
        ];

        Reporter::paySaveCardResponseSuccess($this->params['customer_id'], $this->params['payment_id']);

        Response::json($data)->send();
        die();

    }

    /**
     *
     * ответ для нашего API в нашем формате
     *
     * @param array  $data
     * @param string $error
     * @param string $message
     * @param int    $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function response($data, $error, $message, $code = 200)
    {
        $response = [
            'data'    => $data,
            'errors'  => $error ? [$error] : null,
            'message' => $message,
        ];

        return Response::json($response, $code);
    }

    /**
     * проверка доступа для Агента Агбис
     */
    private function filterAgent()
    {

        Log::debug('export run filter agent', []);

        $login = Input::get('Lg');
        $password = Input::get('Pd');

        if (!$login || !$password) {
            Log::debug('empty credentials', Input::all());
            Response::json(['error' => 1])->send();
            die();
        }

        $login = substr($login, 0, 50);
        $password = substr($password, 0, 50);

        $checkLogin = Config::get('agbis.agent.login');
        $checkPassword = Config::get('agbis.agent.password');


        if (
            $login != $checkLogin ||
            ($password != $checkPassword && $password != sha1($checkPassword))
        ) {
            Log::debug('agent export auth failed', Input::all());
            Response::json(['error' => 1])->send();
            die();
        }

    }

    private function getAgentGuid()
    {
        $guid = trim(Input::get('guid'));

        if (empty($guid)) {
            Response::json(['error' => 1])->send();
            die();
        }

        return $guid;

    }

    private function processAgentExport($guid)
    {

        // флаг запуска именно этого процесса
        $doExport = Input::get('Load', null);
        if (null === $doExport) {
            return;
        }

        Log::debug('agent export request process', ['guid' => $guid]);

        // все оплаченные не выгруженные заказы
        $list = PaymentCloud::payed()->notExported()->notFailed()->get()->all();

        Log::debug('search transactions for export', ['qnt' => count($list)]);

        if (0 == count($list)) {
            Log::debug('response error', []);
            Response::json(['error' => 1])->send();
            die();
        }

        $xml = new GenerateAgentXML();

        foreach ($list as $pay) {

            $a = $xml->addEl('Pay');

            $pan = $pay->card_pan
                ? substr($pay->card_pan, -4)
                : '0000';

            $type = $pay->card_type
                ? $pay->card_type
                : 'YandexWallet';

            $xml->addEl('mysql_id', $pay->id, $a);
            $xml->addEl('dor_id', $pay->order_id, $a);
            $xml->addEl('contr_id', $pay->customer_id, $a);
            $xml->addEl('amount', $pay->amount, $a);
            $xml->addEl('token', $pay->token, $a);
            $xml->addEl('card_last_four', $pan, $a);
            $xml->addEl('card_type', $type, $a);
            $xml->addEl('pay_system_id', $pay->getPaySystemId(), $a);

            $pay->guid = $guid;
            $pay->save();

            Log::debug('set guid into payment transaction', ['id' => $pay->id]);

        }

        $xml->output();
        Log::debug('response export xml', []);
        die();

    }


    /**
     * подтверждение от Агента, что транзакция была обработана
     * по всем транзакциям, которые прежде были выгружены с этим guid
     *
     * @param string $guid
     */
    private function processAgentCommit($guid)
    {

        // флаг запуска именно этого процесса
        $doCommit = Input::get('SavePay', null);
        if (null === $doCommit) {
            return;
        }

        Log::debug('agent commit payment request process', ['guid' => $guid]);
        $qnt = PaymentCloud::whereGuid($guid)->count();
        Log::debug('search transactions for commit', ['qnt' => $qnt]);

        if (0 == $qnt) {
            Log::debug('response error', []);
            Response::json(['error' => 1])->send();
            die();
        }

        PaymentCloud::whereGuid($guid)->update([
            'exported'    => 1,
            'exported_at' => DB::raw('NOW()'),
        ]);

        $xml = new GenerateAgentXML();
        $xml->addEl('Error', 0);
        $xml->output();
        Log::debug('response commit xml', []);
        die();

    }

    private function saveCard()
    {
        $card = CloudPaymentsCard::whereCustomerCard($this->params['card_pan'], $this->params['customer_id']);
        if ($card) {
            // card is exist message and return
        }
        else {
            $card = new CloudPaymentsCard();
            unset($this->params['order_id']);
            $card->fill($this->params);
            $card->save();

            $this->responseAddCardSuccess();
        }

    }
}