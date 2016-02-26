<?php


namespace Dryharder\Manage\Controllers;


use Cache;
use Dryharder\Agbis\Api;
use Dryharder\Agbis\ApiException;
use Dryharder\Components\Customer;
use Dryharder\Components\Mailer;
use Dryharder\Components\Order;
use Dryharder\Gateway\Models\PaymentCloud;
use Dryharder\Models\Customer as CustomerModel;
use Dryharder\Models\OrderAutopay;
use Response;
use Input;

class AutoPayController extends BaseController
{
    private $orderInfo;

    public function index()
    {
        if (!$this->isAcceptedJson()) {
            return \View::make('man::autopays.index');
        }

        $orderInfo = [
            'countOrders' => 0,
            'totalOrderAmount' => 0
        ];

        $list = CustomerModel::getAutopayAll();

        $result = [];
        foreach ($list as $customer) {
            $data = [
                'phone' => $customer->phone,
                'agbisId' => $customer->agbis_id,
                'agbisPassword' => $customer->agbis_password
            ];
            $this->checkPaidOrders($data);

            $orderInfo['countOrders'] += $this->orderInfo['totalOrder'];
            $orderInfo['totalOrderAmount'] += $this->orderInfo['totalOrderAmount'];

            $result[] = [
                'id' => $customer->customer_id,
                'agbisId' => $customer->agbis_id,
                'agbisId' => $customer->agbis_id,
                'email' => $customer->email,
                'name' => $customer->name,
                'isGoodOrder' => $this->orderInfo['totalOrder'] > 0 ? true : false
            ];
        }

        return [
            'list' => $result,
            'orderInfo' => $orderInfo
        ];

    }

    private function checkPaidOrders($data)
    {
        $this->orderInfo = [
            'totalOrder' => 0,
            'totalOrderAmount' => 0
        ];

        $api = new Api();

        $customerId = $data['agbisId'];
        $phone = '+7' . $data['phone'];
        $user = $api->Login_con($phone, $data['agbisPassword']);
        $key = $user->key;
        $orders = $api->Orders($key)['orders'];
        foreach ($orders as $order) {
            if ($this->isNotPaidOrder($customerId, $order['id'])) {
                if ($api->IsGoodOrder($order['id'], $customerId)) {
                    $this->orderInfo['totalOrder'] ++;
                    $this->orderInfo['totalOrderAmount'] += $order['amount'];
                }
            }
        }
    }

    public function checkCustomersOrders($customerId)
    {
        $customer = Customer::instance()->initByExternalId($customerId);

        $this->orderInfo = [
            'totalOrder' => 0,
            'totalOrderAmount' => 0
        ];
        $data = [
            'phone' => $customer->get()->phone,
            'agbisId' => $customerId,
            'agbisPassword' => $customer->get()->credential->agbis_password
        ];
        $this->checkPaidOrders($data);

        $isGoodOrder = $this->orderInfo['totalOrder'] > 0 ? true : false;

        return Response::json([
            'isGoodOrder' => $isGoodOrder,
        ], 200);
    }

    public function autopayAll()
    {
        $result = [];
        $errors = [];
        if (Input::has('customers')) {
            $api = new Api();
            $customers = Input::get('customers');
            foreach ($customers as $customerId) {
                $customer = Customer::instance()->initByExternalId($customerId);
                if ($customer) {
                    $card = PaymentCloud::getCustomerAutopayCard($customerId);
                    if ($card) {
                        $phone = '+7' . $customer->get()->phone;
                        $password = $customer->get()->credential->agbis_password;
                        $user = $api->Login_con($phone, $password);
                        $key = $user->key;
                        $orders = $api->Orders($key)['orders'];
                        foreach ($orders as $order) {
                            if ($this->isNotPaidOrder($customerId, $order['id'])) {
                                if ($api->IsGoodOrder($order['id'], $customerId)) {
                                    if($this->isNotPaidOrder($customerId, $order['id'])){
                                        OrderAutopay::unguard();
                                        $pay = OrderAutopay::create([
                                            'order_id'    => $order['id'],
                                            'customer_id' => $customerId,
                                            'state'       => 0,
                                        ]);

                                        $payResult = $api->payByToken($order['id'], $card->token, $order['amount'], $order['doc_number'], $key);
                                        $pay->comment = $payResult->message;
                                        $pay->save();

                                        if($payResult->success){
                                            $pay->state = 1;
                                            $pay->save();
                                            Mailer::succesAutoPay($customer, $order, $card);
                                        }
                                        else {
                                            $errors[] = [
                                                'customer' => $customer->get(),
                                                'order' => $order,
                                                'message' => $payResult->message
                                            ];
                                            Mailer::errorAutoPay($customer, $order, $card);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $data = [
                        'phone' => $customer->get()->phone,
                        'agbisId' => $customerId,
                        'agbisPassword' => $customer->get()->credential->agbis_password
                    ];
                    $this->checkPaidOrders($data);

                    $result[] = [
                        'customerId' => $customerId,
                        'isGoodOrder' => $this->orderInfo['totalOrder'] > 0 ? true : false
                    ];
                }
            }
        }

        return Response::json([
            'result' => $result,
            'errors' => $errors
        ], 200);
    }

    public function start($order_id, $customer_id)
    {

        if(!$this->isNotPaidOrder($customer_id, $order_id)){
            return Response::json([
                'message' => 'Заказ находится в процессе оплаты',
            ], 500);
        }

        $customer = Customer::instance()->initByExternalId($customer_id);
        if(!$customer){
            return Response::json([
                'message' => 'Клиент не найден',
            ], 500);
        }

        $autopayCard = PaymentCloud::getCustomerAutopayCard($customer_id);
        if(!$autopayCard){
            return Response::json([
                'message' => 'Автоплатежи клиента выключены',
            ], 500);
        }

        $key = 'users.agbis.sessions.customer' . $customer_id;
        $sessionId = $this->initSessionKey($customer, $customer->get()->credential->agbis_password, $key);

        if(!$sessionId){
            return Response::json([
                'message' => 'Сессия клиента не найдена',
            ], 500);
        }

        $api = new Api();
        $order = $api->getOrder($order_id, $sessionId);

        if(!$order){
            return Response::json([
                'message' => 'Заказ клиента не найден',
            ], 500);
        }

        if($autopayCard->token == ''){
            return Response::json([
                'message' => 'Токен привязанной карты не найден',
            ], 500);
        }

        OrderAutopay::unguard();
        $pay = OrderAutopay::create([
            'order_id'    => $order_id,
            'customer_id' => $customer_id,
            'state'       => 0,
        ]);

        $result = $api->payByToken($order_id, $autopayCard->token, $order['amount'], $order['doc_number'], $sessionId);
        $pay->comment = $result->message;
        $pay->save();

        if($result->success){
            $pay->state = 1;
            $pay->save();
            Mailer::succesAutoPay($customer, $order, $autopayCard);
            return Response::json([
                'amount' => $order['amount']
            ]);
        }

        Mailer::errorAutoPay($customer, $order, $autopayCard);
        return Response::json([
            'message' => $result->message,
        ], 500);

    }

    private function isNotPaidOrder($customerId, $orderId)
    {
        $orders = PaymentCloud::getCustomersPaidOrders($customerId);

        if (!in_array($orderId, $orders)) {
            return true;
        }
        return false;
    }

    public function orders($cid)
    {

        $customer = Customer::instance()->initByExternalId($cid);
        $api = new Api();

        $password = $customer->get()->credential->agbis_password;
        if (!$password) {
            $this->log('Customer password is not exists', ['customer_id' => $cid]);

            return Response::json('Customer password is not exists', 500);
        }

        $key = 'users.agbis.sessions.customer' . $cid;
        $sessionId = Cache::get($key);

        if ($sessionId) {
            $this->log('session found', ['customer' => $customer->get()->id, 'session' => $sessionId]);
        }

        if (!$sessionId) {
            $sessionId = $this->initSessionKey($customer, $password, $key);
        }

        try {
            $orders = $api->Orders($sessionId);
        } catch (ApiException $e) {
            $this->log('session inactive, try new session');
            $sessionId = $this->initSessionKey($customer, $password, $key);
            $orders = $api->Orders($sessionId);
        }

        foreach ($orders['orders'] as &$order) {
            $order['status_name'] = Order::statusName($order['status']);
            $order['autopay'] = ($order['status'] >= 1 && $order['amount'] > 0);
            $lastPay = OrderAutopay::getLastPay($cid, $order['id']);
            $order['order_autopay'] = $lastPay['lastPay'];
            $order['payTotal'] = $lastPay['total'];
        }

        $orders = array_reverse($orders['orders']);

        return $orders;

    }


    private function log($message, $data = null)
    {
        \Log::debug($message . ': ' . print_r($data, true), []);
    }

    /**
     * @param Customer $customer
     * @param $password
     * @param $key
     *
     * @return array
     */
    public function initSessionKey(Customer $customer, $password, $key)
    {
        $api = new Api();
        $user = $api->Login_con('+7' . $customer->get()->phone, $password);
        $sessionId = $user->key;
        Cache::put($key, $sessionId, 500);

        $this->log('get new session', ['customer' => $customer->get()->id, 'session' => $sessionId]);

        return $sessionId;
    }


}