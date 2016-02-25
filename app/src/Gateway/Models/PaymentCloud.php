<?php


namespace Dryharder\Gateway\Models;

/**
 * Class PaymentCloud
 *
 * @property integer $id
 * @property string  $guid
 * @property integer $customer_id
 * @property integer $order_id
 * @property integer $payment_id
 * @property float   $amount
 * @property string  $created_at
 * @property string  $updated_at
 * @property string  $external_at
 * @property string  $exported_at
 * @property string  $token
 * @property string  $card_pan
 * @property string  $card_type
 * @property string  $card_holder
 * @property string  $ip
 * @property string  $email
 * @property string  $request
 * @property integer $exported
 * @property integer $waiting
 * @property integer $failed
 *
 * @method static PaymentCloud whereCustomerId($customer_id)
 * @method static PaymentCloud wherePaymentId($payment_id)
 * @method static PaymentCloud whereOrderId($order_id)
 * @method static PaymentCloud whereWaiting($int)
 * @method static PaymentCloud whereExported($int)
 * @method static PaymentCloud whereGuid($guid)
 * @method static PaymentCloud whereNotFailed()
 * @method static PaymentCloud whereFailed($boolean)
 * @method static PaymentCloud first()
 * @method static PaymentCloud payed()
 * @method static PaymentCloud notFailed()
 * @method static PaymentCloud notExported()
 * @method static PaymentCloud processing()
 * @method static PaymentCloud[] all()
 * @method static PaymentCloud get()
 *
 */
class PaymentCloud extends \Eloquent
{

    protected $table = 'payment_cloud';

    /**
     * @param $customer_id
     *
     * @return PaymentCloud|null
     */
    public static function getToken($customerId, $paymentId)
    {
        // последняя успешная оплата клиента, в которой есть токен
        /*$lastPay = self::payed()
            ->whereCustomerId($customerId)
            ->notFailed()
            ->whereWaiting(0)
            ->where('token', '!=', '')
            ->orderBy('id', 'desc')
            ->first();*/

        $tokenCard = self::whereCustomerId($customerId)
            ->wherePaymentId($paymentId)
            ->first();



        return ($tokenCard && $tokenCard->token) ? $tokenCard : null;

    }

    /**
     * найти заказ, который находится в процессе оплаты
     * (ожидает оплаты, не было ошибки)
     *
     * @param $id
     *
     * @return PaymentCloud
     */
    public static function getInProcessing($id)
    {

        $pay = self::whereOrderId($id)
            ->where(function ($query) {
                /** @var PaymentCloud $query */
                $query->processing();
            })
            ->notFailed()
            ->first();

        // нет заказа
        if (!$pay) {
            return false;
        }

        // закрыть заказ если он "висит" уже долго
        if ($pay->closeIfLongWaiting()) {
            return false;
        }

        // заказ есть и он ждет оплаты
        return $pay;

    }

    /**
     * удаление всех токенов платежей клиента
     *
     * @param integer $customer_id
     */
    public static function removeTokens($customer_id)
    {
        self::whereCustomerId($customer_id)
            ->where('token', '!=', '')
            ->update([
                'token' => '',
            ]);
    }

    public static function stateOrder($customerId, $orderId)
    {

        $order = self::whereCustomerId($customerId)
            ->whereOrderId($orderId)
            ->orderBy('id', 'desc')
            ->first();

        $state = 'error';

        if (!$order) {
            $message = trans('main.Order not found');
        } elseif ($order->failed) {
            $message = trans('main.Order payment is failed');
        } elseif ($order->waiting) {
            $message = trans('main.Order payment is waiting');
            $state = 'progress';
        } else {
            $message = trans('main.Order payment is success');
            $state = 'success';
        }

        return [
            'state'   => $state,
            'message' => $message,
        ];

    }

    /**
     * подтверждение оплаты транзакции
     *
     * @param $token
     *
     * @return bool
     */
    public function paid($token, $isNew = false)
    {

        $this->failed = 0;
        if ($isNew) {
            $this->token = $token;
        }
        $this->waiting = 0;

        return $this->save();

    }

    /**
     * подтверждение оплаты транзакции от я-денег
     *
     * @return bool
     */
    public function paidYam()
    {

        $this->failed = 0;
        $this->waiting = 0;

        return $this->save();

    }

    /**
     * поиск транзакции, по которой подтверждена оплата
     *
     * @param PaymentCloud $query
     *
     * @return PaymentCloud
     */
    public function scopePayed($query)
    {

        return $query->whereWaiting(0);

    }

    /**
     * поиск транзакции без ошибок
     *
     * @param PaymentCloud $query
     *
     * @return PaymentCloud
     */
    public function scopeNotFailed($query)
    {

        return $query->whereFailed(0);

    }

    /**
     * отбор не выгруженных во внешнюю систему заказов
     *
     * @param PaymentCloud $query
     *
     * @return PaymentCloud
     */
    public function scopeNotExported($query)
    {

        return $query->whereExported(0);

    }

    /**
     * отбор заказов, которые находятся в обработке
     *
     * это:
     * (ожидающий оплаты) ИЛИ (оплаченный не-выгруженный)
     *
     * @param PaymentCloud $query
     *
     * @return PaymentCloud
     */
    public function scopeProcessing($query)
    {
        $query->where(function ($query) {
            /** @var PaymentCloud $query */
            $query->where('waiting', 1);
        })->orWhere(function ($query) {
            /** @var PaymentCloud $query */
            $query->where('waiting', 0)->where('exported', 0);
        });

    }

    /**
     * закрыть заказ как ошибочный, если он слишком долго ждет оплаты
     *
     * @return bool был закрыт?
     */
    private function closeIfLongWaiting()
    {

        // заказ уже ошибочен или была принята оплата
        if ($this->failed == 1 || $this->waiting == 0) {
            // ничего не делаем
            return false;
        }

        $curTime = time();
        $updTime = strtotime($this->updated_at);
        $timeout = $curTime - $updTime;

        // если заказ ожидает оплаты и последний раз обновлялся больше часа назад
        if ($timeout > 60 * 60) {
            $this->failed = 1;
            $this->save();

            return true;
        }

        return false;

    }


    public static function failOrder($order_id)
    {

        $pay = self::whereOrderId($order_id)->whereWaiting(1)->whereFailed(0)->first();
        if ($pay) {
            $pay->failed = 1;
            $pay->save();
        }

    }


    public static function getPayCardType4Yam($pc = null)
    {

        $types = [
            'PC' => 'YaWallet',
            'AC' => 'YaCard',
            'MC' => 'YaMobile',
            'GP' => 'YaTerm',
            'WM' => 'YaWm',
            'SB' => 'YaSberbank',
            'MP' => 'YaMpos',
            'AB' => 'YaAlfaClick',
            'МА' => 'YaMasterPass',
            'PB' => 'YaPromBank',
        ];

        if (isset($types[$pc])) {
            return $types[$pc];
        }

        return 'YaWallet';

    }


    public function getPaySystemId()
    {

        $types = [

            'YaWallet'     => '1',
            'YaCard'       => '1',
            'YaMobile'     => '1',
            'YaTerm'       => '1',
            'YaWm'         => '1',
            'YaSberbank'   => '1',
            'YaMpos'       => '1',
            'YaAlfaClick'  => '1',
            'YaMasterPass' => '1',
            'YaPromBank'   => '1',

            'Visa'         => '1',
            'Maestro'      => '1',
            'MasterCard'   => '1',

        ];

        if(isset($types[$this->card_type])){
            return $types[$this->card_type];
        }

        return '1';

    }

    public static function autopayEnable($customerId, $paymentId = null)
    {
        self::whereCustomerId($customerId)
            ->update([
                'autopay' => 0
            ]);
        if ($paymentId) {
            $pay = self::wherePaymentId($paymentId)->first();
        }
        else {
            $pay = self::whereCustomerId($customerId)
                ->notFailed()
                ->whereWaiting(0)
                ->where('token', '!=', '')
                ->orderBy('id', 'DESC')
                ->first();
        }

        if ($pay) {
            $pay->autopay = 1;
            $pay->save();
            return $pay;
        }
        else {
            return null;
        }
    }

    public static function autopayDisable($customerId)
    {
        self::whereCustomerId($customerId)
            ->update([
                'autopay' => 0
            ]);
    }

    public static function getCustomersCards($customerId)
    {
        return self::whereCustomerId($customerId)
            ->notFailed()
            ->whereWaiting(0)
            ->where('token', '!=', '')
            ->get();
    }

    public static function getLastPay($customerId)
    {
        $lastPay = self::whereCustomerId($customerId)
            ->orderBy('id', 'DESC')
            ->first();

        if (!$lastPay) {
            return null;
        }

        if ($lastPay->failed || $lastPay->waiting) {
            return null;
        }

        return $lastPay;
    }

    public static function checkTokenCard($customerId, $cardPan)
    {
        return self::whereCustomerId($customerId)
            ->where('card_pan', '=', $cardPan)
            ->where('token', '!=', '')
            ->notFailed()
            ->whereWaiting(0)
            ->first();
    }

    public static function finishPay($cusomerId, $saveCard, $autopay)
    {
        $lastPay = self::getLastPay($cusomerId);
        if ($lastPay) {
            if (!$saveCard) {
                $lastPay->token = '';
            }
            elseif ($saveCard && $autopay) {
                $lastPay->autopay = 1;
            }
            $lastPay->save();

            return $lastPay;
        }
        else {
            return null;
        }
    }

    public static function deleteCard($customerId, $payments)
    {
        self::whereCustomerId($customerId)
            ->whereIn('payment_id', $payments)
            ->update([
                'token' => '',
                'autopay' => 0
            ]);
    }

    public static function getCustomerAutopayCard($customerId)
    {
        return self::whereCustomerId($customerId)
            ->notFailed()
            ->whereWaiting(0)
            ->where('autopay', '=', 1)
            ->first();
    }

    public static function getCustomersPaidOrders($customerId)
    {
        $orders = self::whereCustomerId($customerId)
            ->notFailed()
            ->whereWaiting(0)
            ->get();
        $ids = [];
        foreach ($orders as $order) {
            $ids[] = $order->order_id;
        }

        return $ids;
    }
<<<<<<< HEAD
=======

    public static function checkPaid($orderId)
    {
        $order = self::whereOrderId($orderId)
            ->notFailed()
            ->where(function($query) {
                $query
                    ->where('waiting', 1)
                    ->orWhere('exported', 1)
                    ->orWhere(function($query) {
                        /** @var PaymentCloud $query */
                        $query->where('waiting', 0)->where('exported', 0);
                    });
            })
            ->first();

        if (!$order) {
            return false;
        }

        // закрыть заказ если он "висит" уже долго
        if ($order->closeIfLongWaiting()) {
            return false;
        }

        return true;
    }

    public static function lastAddedCard($customerId)
    {
        return self::whereCustomerId($customerId)
            ->whereWaiting(0)
            ->notFailed()
            ->where('token', '!=', '')
            ->orderBy('id', 'desc')
            ->first();
    }
>>>>>>> bdbe29180145219d37259cbbbce228fe77dc3501
}