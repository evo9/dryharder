<?php


namespace Dryharder\Components;


use Dryharder\Agbis\Api;
use Dryharder\Agbis\ApiException;
use Dryharder\Models\NotifyOrder;
use Dryharder\Models\OrderRequest;
use mPDF;
use NotifyOrderCommand;
use View;

class NotifyOrderComponent
{

    private $api;
    private $keys;

    public function __construct(NotifyOrderCommand $console)
    {
        $this->api = new Api();
        $this->console = $console;
    }

    public function fire()
    {
        $orders = $this->api->lastDayOrders();
        $this->log('found last orders', ['qnt' => count($orders)]);

        foreach ($orders as $order) {
            $this->initOrderInfo($order->dor_id, $order->contr_id);
        }

        $notifyOrders = NotifyOrder::whereSent(0)
            ->whereRaw('created_at <= DATE_SUB(NOW(), INTERVAL 1 HOUR)')
            ->get()
            ->all();
        $this->log('found actual notify orders', ['qnt' => count($notifyOrders)]);

        foreach ($notifyOrders as $notify) {
            $this->notifyOrderInfo($notify);
        }

    }

    private function initOrderInfo($orderId, $customerId)
    {

        $notifyExist = NotifyOrder::whereOrderId($orderId)->exists();
        if ($notifyExist) {
            return;
        }

        $notifyOrder = new NotifyOrder();
        $notifyOrder->order_id = $orderId;
        $notifyOrder->customer_id = $customerId;
        $notifyOrder->sent = 0;
        $notifyOrder->save();

    }

    private function notifyOrderInfo(NotifyOrder $notify)
    {

        $this->log('consume notify', $notify->getAttributes());

        $customerId = $notify->customer_id;
        $orderId = $notify->order_id;

        $customer = Customer::instance()->initByExternalId($customerId);

        if (!$customer) {
            $this->log('Customer is not initialized', ['customer_id' => $customerId]);

            return;
        }

        if(!$customer->get()->email){
            $this->log('Email is empty', ['customer_id' => $customerId]);
            $notify->sent = 1;
            $notify->save();
            return;
        }


        $password = $customer->get()->credential->agbis_password;
        if (!$password) {
            $this->log('Customer password is not exists', ['customer_id' => $customerId]);

            return;
        }

        if (isset($this->keys[$customerId])) {
            $key = $this->keys[$customerId];
        } else {
            try{
                $user = $this->api->Login_con('+7' . $customer->get()->phone, $password);
            }catch (ApiException $e){
                $this->log($e->getMessage(), [$customerId]);
                $notify->sent = 1;
                $notify->save();
                return;
            }
            $this->keys[$customerId] = $user->key;
            $key = $user->key;
        }

        $order = $this->api->getOrder($orderId, $key);
        $services = (new OrderServiceComponent())->parseOrderService($orderId, $key);
        $email = $customer->get()->email;
        $name = $customer->get()->name;

        $this->log('ready data send', ['services' => count($services)]);

        try {
            
            $attach = self::createClothesFile($order, $services, $name);
            Mailer::notifyNewOrder($order, $services, $email, $name, $attach);
            
            $notify->sent = 1;
            $notify->save();

            OrderRequest::markAsCompleted($customer->get()->phone, $orderId);

        } catch (\Exception $e) {
            $this->log('send mail error', [
                'email'   => $customer->get()->email,
                'id'      => $customer->get()->id,
                'message' => $e->getMessage(),
            ]);
        }

    }

    private function log($message, $data)
    {
        $this->console->line($message . ': ' . print_r($data, true));
    }

    public static function createClothesFile($order, $services, $name, $show = false)
    {
        $contents = View::make('ac::notify', compact('order', 'services', 'name'))->render();
        $name = 'Dryharder order description ' . $order['doc_number'] . '.pdf';
        $path = storage_path('views/' . $name);

        $pdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10);
        $pdf->charset_in = 'utf8';

        $stylesheet = View::make('ac::notify-styles')->render();
        $pdf->WriteHTML($stylesheet, 1);

        $pdf->list_indent_first_level = 0;
        $pdf->WriteHTML($contents, 2);

        if ($show) {
            return \Response::make($pdf->Output($name, 'S'), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $name . '"',
            ]);
        }

        $pdf->Output($path, 'F');

        return $path;

    }


}