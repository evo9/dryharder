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

class AutoPayController extends BaseController
{


    public function index()
    {
        if (!$this->isAcceptedJson()) {
            return \View::make('man::autopays.index');
        }

        $list = CustomerModel::where('auto_pay', 1)->get()->all();

        foreach ($list as $customer) {
            $token = PaymentCloud::getToken($customer->agbis_id);
            $customer->tokenExists = !empty($token);
        }

        return $list;

    }

    public function start($order_id, $customer_id)
    {

        $pay = OrderAutopay::whereOrderId($order_id)->first();

        if($pay){
            return Response::json([
                'message' => 'Уже создана заявка на автосписание по этому заказу',
            ], 500);
        }

        $customer = Customer::instance()->initByExternalId($customer_id);
        if(!$customer){
            return Response::json([
                'message' => 'Клиент не найден',
            ], 500);
        }

        if(!$customer->isSaveCard() || !$customer->isAutoPay()){
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

        $token = PaymentCloud::getToken($customer_id);
        if(!$token){
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

        $result = $api->payByToken($order_id, $token->token, $order['amount'], $order['doc_number'], $sessionId);
        $pay->comment = $result->message;
        $pay->save();

        if($result->success){
            $pay->state = 1;
            $pay->save();
            Mailer::succesAutoPay($customer, $order, $token);
            return Response::json([]);
        }

        Mailer::errorAutoPay($customer, $order, $token);
        return Response::json([
            'message' => $result->message,
        ], 500);

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
            $order['order_autopay'] = OrderAutopay::whereCustomerId($cid)->whereOrderId($order['id'])->first();
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