<?php

namespace Dryharder\Account\Controllers;

use App;
use Config;
use Dryharder\Agbis\Api;
use Dryharder\Agbis\ApiException;
use Dryharder\Components\Customer;
use Dryharder\Components\InviteComponent;
use Dryharder\Components\Mailer;
use Dryharder\Components\NotifyOrderComponent;
use Dryharder\Components\Order;
use Dryharder\Components\OrderServiceComponent;
use Dryharder\Components\Reporter;
use Dryharder\Components\Validation;
use Dryharder\Gateway\Models\PaymentCloud;
use Dryharder\Models\OrderRequest;
use Dryharder\Models\OrderReview;
use Dryharder\Models\ServiceTitle;
use Dryharder\Models\Subscription;
use Input;
use Redirect;
use Response;
use View;
use Exception;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;

/** @noinspection PhpInconsistentReturnPointsInspection */
class MainController extends BaseController
{

    /**
     * личный кабинет
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (Input::get('orderNumber')) {
            return Redirect::to('/account');
        }

        Customer::instance()->closeIfNotMember();

        $promo = null;
        $mainPage = Config::get('app.url');
        $langReplace = App::getLocale() == 'ru' ? 'index' : App::getLocale();
        $mainPage = str_replace('#lang#', $langReplace, $mainPage);
        $cards = [];

        try {
            $api = new Api();
            if (!$api->key()) {
                return \Redirect::to($mainPage);
            }
            $user = $api->ContrInfo();
            $promo = $this->promoInfo($user, $promo);
            //$token = PaymentCloud::getToken($user['id']);
            $agbisKey = $api->key();

            $customer = Customer::instance()->initByExternalId($user['id']);
            if (!$customer->get()) {
                Reporter::errorLostExternalCustomer($user['id'], $user);
                $customer->cleanup();
                $api->cleanup();

                return \Redirect::to($mainPage);
            }
            $saveCard = $customer->isSaveCard();

            $cards = PaymentCloud::getCustomersCards($api->id());

        } catch (ApiException $e) {
            return \Redirect::to($mainPage);
        }

        $invite = new InviteComponent();

        return View::make('ac::index', [
            'user'       => $user,
            'promo'      => $promo,
            'agbisKey'   => $agbisKey,
            'saveCard'   => $saveCard,
            'invite_url' => $invite->url(),
            'cards'       => $cards
        ]);

    }

    public function customersCards()
    {
        $api  =new Api();
        $cards = PaymentCloud::getCustomersCards($api->id());

        return View::make('ac::inc.customers-cards', [
            'cards' => $cards
        ]);
    }

    public function bonus()
    {
        $api = new Api();

        return $api->Bonus();
    }

    public function subscriptions()
    {
        $api = new Api();
        $list = $api->Certificate();
        $actives = $api->ActiveCertificates();

        return View::make('ac::inc.subscriptions-inc', [
            'list'    => $list,
            'actives' => $actives,
        ]);
    }


    public function card()
    {

        $api = new Api();

        $token = PaymentCloud::getToken($api->id());
        $saveCard = Customer::instance()->initByExternalId($api->id())->isSaveCard();
        $autoPay = Customer::instance()->isAutoPay();

        return View::make('ac::card', [
            'token'    => $token,
            'saveCard' => $saveCard,
            'autoPay'  => $autoPay,
        ]);

    }

    public function prepayment()
    {
        $params = [
            'cards' => [],
            'lastPay' => [
                'payment_id' => '-1',
                'card_pan' => trans('pay.prepayment.new_card')
            ]
        ];

        $api = new Api();
        $customerId = $api->id();

        $cards = PaymentCloud::getCustomersCards($customerId);
        $cPan = [];
        foreach ($cards as $card) {
            $cPan[] = $card['card_pan'];
            $params['cards'][] = [
                'payment_id' => $card['payment_id'],
                'card_pan' => $card['card_pan']
            ];
        }
        if (count($cPan) > 0) {
            $lastPay = PaymentCloud::getLastPay($customerId);
            if ($lastPay && in_array($lastPay['card_pan'], $cPan)) {
                $params['lastPay'] = [
                    'payment_id' => $lastPay['payment_id'],
                    'card_pan' => $lastPay['card_pan']
                ];
            }
        }

        return View::make('ac::prepayment', $params);
    }


    public function checkPay($id)
    {
        $api = new Api();
        $state = PaymentCloud::stateOrder($api->id(), $id, true);

        return Response::json([
            'data'    => $state,
            'message' => $state['message'],
        ]);

    }


    public function formCardSave()
    {

        $as = Input::get('as');

        $as = $as ? 1 : 0;
        $api = new Api();

        $customer = Customer::instance()->initByExternalId($api->id());

        if(!$as && $customer->isAutoPay()){
            $customer->setAutopay(false);
        }

        return (int)$customer->setSaveCard($as)->isSaveCard();

    }

    public function formCardRemove()
    {

        $api = new Api();
        PaymentCloud::removeTokens($api->id());

    }

    public function formAutopaySave()
    {

        $as = Input::get('as');

        $as = $as ? 1 : 0;
        $api = new Api();

        $customer = Customer::instance()->initByExternalId($api->id());

        if(!$customer->isSaveCard()){
            return $this->responseErrorMessage('Автоплатеж возможет только тогда, когда вы сохраняете карту для дальнейших платежей', 404);
        }

        return (int)Customer::instance()
            ->initByExternalId($api->id())
            ->setAutopay($as)
            ->isAutoPay();

    }

    public function token()
    {
        if (!Input::has('payment_id')) {
            return Response::json([
                'errors'    => [],
                'message' => trans('pay.prepayment.search_card_error')
            ]);
        }

        $paymentId = Input::get('payment_id');
        $target = Input::get('target');
        $order_id = $this->parsePayTarget($target, Input::get('id'));

        // проверим id
        $order_id = (int)$order_id;
        if ($order_id <= 0) {
            return $this->responseErrorMessage('Отсутствует id заказа', 400);
        }

        // проверим существование заказа у данного клиента
        $api = new Api();
        $order = $api->getOrder($order_id);
        if (!$order) {
            if ($target == 'subscription') {
                return $this->responseErrorMessage('Вероятно, эта подписка уже была оплачена', 404);
            }

            return $this->responseErrorMessage('Заказ не найден', 404);
        }

        // проверим, что заказ не находится в процессинге оплаты
        $check = $this->isOrderPayWaiting($order_id);
        if ($check !== true) {
            return $check;
        }

        // проверим, что заказ можно оплатить в api
        try {
            $api->IsGoodOrder($order_id, $api->id());
        } catch (ApiException $e) {
            return $this->responseErrorMessage('Оплата заказа не доступна', 403);
        }

        $token = PaymentCloud::getToken($api->id(), $paymentId);
        if ($token) {
            $result = $api->payByToken($order_id, $token->token, $order['amount'], $order['doc_number']);
            if (!$result->success) {
                return $this->responseErrorMessage($result->message, 200);
            }

            return Response::json([
                'data'    => '',
                'message' => $result->message,
            ]);
        }
        return Response::json([
            'errors'    => [],
            'message' => 'Ошибка! Карта не найдена!',
        ]);

    }

    /**
     * данные для форм
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forms()
    {

        $response = [];

        $api = new Api();
        $user = $api->ContrInfo();

        $response['user'] = array_map('htmlspecialchars', $user);

        return Response::json($response);

    }

    /**
     * изменение данных клиентом
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function formUser()
    {

        Validation::prepareInput(['phone', 'phone2']);
        $fields = ['name', 'phone', 'phone2', 'email', 'address'];

        $validator = Validation::validator($fields);
        if ($validator->fails()) {
            return $this->responseError($validator, 'Некорректные данные');
        }

        $api = new Api();
        $api->SaveInfo(Input::only($fields));

        return Response::json([
            'data'    => '',
            'message' => 'Данные успешно сохранены',
        ]);

    }

    /**
     * изменение пароля клиентом
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function formPassword()
    {

        $api = new Api();
        $customer = Customer::instance()->initByExternalId($api->id());
        $check = $customer->checkPassword(Input::get('password'));

        if (!$check) {
            return $this->responseErrorMessage('неправильно введен текущий пароль', 400);
        }

        $p1 = Input::get('password1');
        $p2 = Input::get('password2');
        if (!$p1) {
            return $this->responseErrorMessage('введите новый пароль', 400);
        }
        if ($p1 != $p2) {
            return $this->responseErrorMessage('повторите в точности новый пароль', 400);
        }

        $customer->doChangePassword($p1);

        return Response::json([
            'data'    => '',
            'message' => 'Установлен новый пароль',
        ]);

    }

    /**
     * сборная информация по наличию промокода
     *
     * @param $user
     *
     * @return array
     */
    private function promoInfo($user)
    {

        try {
            $api = new Api();
            $promo = $api->PromoCodeUse();
        } catch (ApiException $e) {
        }

        $info = [
            'promo'        => !empty($promo['promo']),
            'address'      => @$promo['address'],
            'discountText' => trans('main.no discount'),
        ];

        if ($info['promo']) {
            if ($promo['discount'] > 0) {
                $info['discountText'] = $promo['discount'] . ' (' . trans('main.corporate tariff') . ')';
            } else {
                $info['discountText'] = trans('main.no discount') . ' (' . trans('main.corporate tariff') . ')';
            }
        }

        if (!empty($user['discount'])) {
            $info['discountText'] = $user['discount'];
        }

        return $info;

    }

    /**
     * список текущих заказов
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders()
    {
        try {

            $api = new Api();
            $orders = $api->Orders()['orders'];

        } catch (ApiException $e) {
            return $this->responseException($e);
        }

        $orders = $this->filterCurrentOrders($orders);
        $qnt = count($orders);

        $customer = Customer::instance()->initByExternalId($api->id());
        $requests = OrderRequest::orderBy('id', 'desc')
            ->whereState(0)
            ->wherePhone($customer->get()->phone)
            ->get();
        $lastOrder = current($orders);
        $lastOrderTime = date('Y-m-d H:i:s', strtotime($lastOrder['date_in']));
        if (!empty($requests[0]) && $requests[0]->created_at <= $lastOrderTime) {
            OrderRequest::markAsCompleted($customer->get()->phone, $lastOrder['id']);
            $requests = [];
        }

        $browser = View::make('ac::orders', [
            'orders'   => $orders,
            'requests' => $requests,
        ])->render();
        $mobile = View::make('ac::orders_mobile', [
            'orders' => $orders,
        ])->render();

        return Response::json(compact('browser', 'mobile', 'qnt'));

    }

    /**
     * история заказов
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function history()
    {

        try {

            $api = new Api();
            $orders = $api->OrdersHistory()['orders'];

        } catch (ApiException $e) {
            return $this->responseException($e);
        }

        $orders = $this->filterHistoryOrders($orders);
        $qnt = count($orders);
        $browser = View::make('ac::orders', [
            'orders' => $orders,
        ])->render();
        $mobile = View::make('ac::orders_mobile', [
            'orders' => $orders,
        ])->render();

        return Response::json(compact('browser', 'mobile', 'qnt'));

    }

    /**
     * список услуг в заказе
     *
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function order($id)
    {
        try {

            $api = new Api();
            $services = $api->Services($id)['services'];

            $showButton = true;
            if (PaymentCloud::checkPaid($id)) {
                $showButton = false;
            }
            $total = 0;
            foreach ($services as $service) {
                $total += $service['amount'];
            }

            // если не русский, будем переводить
            if (App::getLocale() != 'ru') {
                foreach ($services as &$item) {
                    $title = ServiceTitle::whereName($item['name'])->first();
                    if ($title) {
                        $item['name'] = $title->lang(App::getLocale());
                    }
                }
            }

        } catch (ApiException $e) {
            return $this->responseException($e, true);
        }

        return View::make('ac::order', [
            'services' => $services,
            'id'       => $id,
            'showButton' => $showButton,
            'total' => $total
        ]);

    }

    /**
     * подробный список услуг в заказе
     *
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function orderServices($id)
    {

        try {

            $os = new OrderServiceComponent();
            $services = $os->parseOrderService($id);

            $showButton = true;
            if (PaymentCloud::checkPaid($id)) {
                $showButton = false;
            }
            $total = 0;
            foreach ($services as $service) {
                $total += $service['amount'];
            }

            // если не русский, будем переводить
            if (App::getLocale() != 'ru') {
                foreach ($services as &$item) {
                    $title = ServiceTitle::whereName($item['name'])->first();
                    if ($title) {
                        $item['name'] = $title->lang(App::getLocale());
                    }
                }
            }

        } catch (ApiException $e) {
            return $this->responseException($e, true);
        }

        return View::make('ac::order', [
            'services' => $services,
            'id'       => $id,
            'showButton' => $showButton,
            'total' => $total
        ]);

    }

    /**
     * подробный список услуг в заказе
     *
     * @param $id
     *
     * @return \Illuminate\View\View
     *
     */
    public function orderServicesPdf($id)
    {

        try {
            $api = new Api();
            $os = new OrderServiceComponent();
            $order = $api->getOrder($id);
            $services = $os->parseOrderService($id);
            $customer = Customer::instance()->initByExternalId($api->id());
            return NotifyOrderComponent::createClothesFile($order, $services, $customer->get()->name, true);

        } catch (\Exception $e) {
            dd($e);
            return Response::make('Ошибка! Попробуйте в другой раз', 200);
        }

    }


    /**
     * данные для оплаты через CloudPayments
     *
     * @param string $target
     * @param string $id
     * @param boolean $reset
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function pay($id, $target, $reset)
    {

        $id = $this->parsePayTarget($target, $id);

        try {

            $api = new Api();
            $orders = $api->Orders()['orders'];

        } catch (ApiException $e) {
            return $this->responseException($e);
        }

        Reporter::payInit($id, $api->id());

        $order = null;
        foreach ($orders as $item) {
            if ($item['id'] == $id) {
                $order = $item;
            }
        }

        // нет такого заказа
        if (!$order) {
            Reporter::payInitOrderLost($id, $api->id());

            return $this->responseErrorMessage('заказ не найден', 404);
        }

        // проверим, что заказ не находится в процессинге оплаты
        $check = $this->isOrderPayWaiting($id);
        if ($check !== true) {
            // флаг $reset требует сброса состояния оплаты заказа
            // поэтому мы делаем ему fail чтобы можно было приступить к новой оплате
            if ($reset) {
                Reporter::payInitOrderReset($id, $api->id());
                PaymentCloud::failOrder($id);
            } else {
                return $check;
            }
        }

        // проверим, что есть сумма к оплате
        if ($order['amount'] <= 0) {
            return $this->responseErrorMessage('Заказ уже оплачен', 403);
        }

        $api->id();
        $data = [
            'publicId'    => Config::get('cloud.PublicId'),
            'description' => 'Оплата в dryharder.me заказа №' . $order['doc_number'],
            'amount'      => $order['amount'],
            'currency'    => 'RUB',
            'invoiceId'   => $order['id'],
            'accountId'   => $api->id(),
            'data'        => [
                'type'      => 'pay_order',
                'contr_id'  => $api->id(),
                'order_id'  => $order['id'],
                'order_num' => $order['doc_number'],
                'amount'    => $order['amount']
            ],
        ];

        Reporter::payInitReady($id, $api->id(), $data);

        return Response::json([
            'data'    => $data,
            'message' => 'данные для оплаты заказа',
        ]);

    }

    /**
     * Добавление новой карты
     */
    public function newCard()
    {
        $api = new Api();

        $data = [
            'publicId'    => Config::get('cloud.PublicId'),
            'description' => 'Добавление карты в dryharder.me ',
            'amount'      => 1,
            'currency'    => 'RUB',
            'accountId'   => $api->id()
        ];

        return Response::json([
            'data' => $data
        ]);
    }

    /**
     * Возврат платежа после добавления карты
     */
    public function refund()
    {
        $api = new Api();

        if (!Input::has('newCard')) {
            return Response::json([
                'data' => '',
                'message' => 'Action not found'
            ]);
        }

        $lastPay = PaymentCloud::getLastPay($api->id());
        if (!$lastPay || $lastPay['amount'] != 1) {
            return $this->responseErrorMessage('Ошибка! Не найден платеж.', 500);
        }

        $result = $api->refundPayment($lastPay['payment_id'], $lastPay['amount']);

        if (!$result->success) {
            return $this->responseErrorMessage($result->message, 500);
        }

        return Response::json([
            'data'    => '',
            'message' => $result->message,
        ]);
    }

    /**
     * Удаление карт
     */
    public function deleteCard()
    {
        if (Input::has('payments')) {
            $api = new Api();

            PaymentCloud::deleteCard($api->id(), Input::get('payments'));
        }

        return Response::json();
    }

    /**
     * Включение/отключение автооплаты.
     */
    public function autopay()
    {
        $api = new Api();

        $currentCard = '';

        if (Input::has('autopay')) {
            $paymentId = Input::has('payment_id') ? Input::get('payment_id') : null;
            $card = PaymentCloud::autopayEnable($api->id(), $paymentId);
            if ($card) {
                $currentCard = '<i class="fa fa-credit-card"></i> ' . $card['card_type'] . ' ***' . substr($card['card_pan'], -4);;
            }
        }
        else {
            PaymentCloud::autopayDisable($api->id());
            $currentCard = '';
        }

        return Response::json(['currentCard' => $currentCard]);
    }

    public function payFinish()
    {
        $api = new Api();
        $saveCard = false;
        $autopay = false;

        $message = '';

        if (Input::has('save_card')) {
            $saveCard = true;
        }
        if (Input::has('autopay')) {
            $autopay = true;
        }

        $lastPay = PaymentCloud::finishPay($api->id(), $saveCard, $autopay);
        if ($lastPay && $autopay) {
            $message = 'autopay_success';
        }
        elseif ($lastPay && $saveCard) {
            $message = 'card_saved';
        }

        return Response::json(['message' => $message]);
    }

    public function reviewOrder()
    {
        $data = Input::only(['stars', 'text', 'order']);

        $api = new Api();
        $user = $api->ContrInfo();

        if(Input::get('request') && Input::get('request') !== 'false'){
            $request = OrderRequest::find($data['order']);
            $order = [
                'id' => 0,
                'doc_number' => $request->getHumanId(),
            ];
            $data['order'] = 0;
        }else{
            $order = $api->getOrder($data['order']);
        }

        $data['email'] = $user['email'];
        $data['phone'] = $user['phone'];
        $data['name'] = $user['name'];
        $data['doc_number'] = $order['doc_number'];

        Mailer::orderReview($data);

        OrderReview::unguard();
        $review = OrderReview::create([
            'customer_id' => Customer::instance()->initByExternalId($user['id'])->get()->id,
            'text'        => $data['text'],
            'stars'       => $data['stars'],
            'doc_number'  => $order['doc_number'],
            'order_id'    => $order['id'],
        ]);

        return Response::json([
            'data'    => $review,
            'message' => 'отзыв сохранен',
        ]);

    }

    public function review($id)
    {
        $api = new Api();
        $cid = Customer::instance()->initByExternalId($api->id())->get()->id;
        $review = OrderReview::whereCustomerId($cid)->find($id);

        if ($review) {
            return Response::json([
                'data'    => $review,
                'message' => 'отзыв найден',
            ]);
        }

        return $this->responseErrorMessage('отзыв к заказу не найден', 404);

    }


    private function isOrderPayWaiting($id)
    {

        $api = new Api();

        // проверим, что заказ не находится в процессинге оплаты
        $cloud = PaymentCloud::getInProcessing($id);
        if ($cloud) {
            Reporter::payInitOrderLocked($id, $api->id(), $cloud->waiting, $cloud->exported);

            // если заказ ожидает коллбэка от шлюза
            if ($cloud->waiting == 1) {

                $m = trans('main.order starting payment');

                return Response::json([
                    'data'    => [
                        'repeatText' => trans('main.order continue payment'),
                    ],
                    'errors'  => [$m],
                    'message' => $m,
                ], 409);

            }

            $m = trans('main.order waiting payment');

            return Response::json([
                'errors'  => [$m],
                'message' => $m,
            ], 423);
        }

        return true;

    }

    private function filterCurrentOrders($orders)
    {

        $api = new Api();
        $cid = Customer::instance()->initByExternalId($api->id())->get()->id;
        $reviews = OrderReview::whereCustomerId($cid)->orderBy('created_at', 'asc')->get()->lists('id', 'order_id');

        foreach ($orders as $key => &$item) {
            if (!Order::isStatusCurrent($item['status'])) {
                unset($orders[$key]);
                continue;
            }
            if (!empty($reviews[$item['id']])) {
                $item['review_id'] = $reviews[$item['id']];
            } else {
                $item['review_id'] = null;
            }
        }

        return array_reverse($orders);

    }

    private function filterHistoryOrders($orders)
    {

        $api = new Api();
        $cid = Customer::instance()->initByExternalId($api->id())->get()->id;
        $reviews = OrderReview::whereCustomerId($cid)->orderBy('created_at', 'asc')->get()->lists('id', 'order_id');

        foreach ($orders as $key => &$item) {
            if (!Order::isStatusHistory($item['status'])) {
                unset($orders[$key]);
                continue;
            }
            if (!empty($reviews[$item['id']])) {
                $item['review_id'] = $reviews[$item['id']];
            }
        }

        return array_reverse($orders);

    }


    protected function responseException(Exception $e, $isView = false)
    {
        if (!$isView) {

            return Response::json([
                'errors'  => [$e->getMessage()],
                'message' => 'Ошибка api'
            ], $e->getCode());

        }

        return View::make('ac::error', [
            'exception' => $e,
        ]);
    }


    /**
     * @param Validator|MessageBag|array $errors
     * @param string $message
     * @param integer $code
     *
     * @return Response
     */
    protected function responseError($errors, $message = '', $code = 400)
    {

        if ($errors instanceof Validator) {
            $errors = $errors->errors();
        }

        if ($errors instanceof MessageBag) {
            $errors = $errors->toArray();
            foreach ($errors as &$value) {
                $value = $value[0];
            }
        }

        return Response::json([
            'errors'  => $errors,
            'message' => $message,
        ], $code);

    }

    /**
     * @param string $message
     * @param integer $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseErrorMessage($message, $code)
    {
        return Response::json([
            'errors'  => [$message],
            'message' => $message,
        ], $code);
    }

    private function parsePayTarget($target, $id)
    {
        $api = new Api();

        $id = (int)$id;
        if ($id <= 0) {
            return null;
        }

        // оплата заказа
        if ($target == 'order') {
            return $id;
        }

        // оплата подписки
        if ($target == 'subscription') {

            $customer_id = Customer::instance()->initByExternalId($api->id())->get()->id;
            $subscription_id = $id;
            Reporter::subscriptionPaymentRequest($customer_id, $subscription_id);

            $subscription = Subscription::whereCustomerId($customer_id)->find($subscription_id);
            if (!$subscription) {

                $list = $api->Certificate();

                // ищем эту подписку в агбисе
                foreach ($list as $item) {
                    if ($item->id == $subscription_id) {

                        Reporter::subscriptionFound($customer_id, $subscription_id, $item);

                        // создаем подписку в нашей базе
                        Subscription::unguard();
                        $subscription = Subscription::create([
                            'id'          => $item->id,
                            'name'        => $item->name,
                            'description' => $item->comments,
                            'price'       => $item->price,
                            'customer_id' => $customer_id,
                            'order_id'    => 0,
                        ]);

                        Reporter::subscriptionCreated($customer_id, $subscription_id);

                    }
                }
            }

            if (!$subscription) {
                Reporter::subscriptionNotFound($customer_id, $subscription_id);

                return null;
            }

            // если заказ уже был создан, отдаем его для оплаты
            if ($subscription->order_id > 0) {
                return $subscription->order_id;
            }

            // создадим заказа в агбисе
            $order_id = $api->CreatePayCertificate($subscription->id);
            if ($order_id > 0) {

                Reporter::subscriptionOrderCreated($customer_id, $subscription_id, $order_id);

                // отдадим для оплаты
                $subscription->order_id = $order_id;
                $subscription->save();

                return $subscription->order_id;
            }

        }

        return null;

    }

}