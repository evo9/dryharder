<?php


namespace Dryharder\Models;


/**
 * @property int $id
 * @property int $customer_id
 * @property int $order_id
 * @property int $amount
 * @property int $state
 * @property string $comment
 * @property string $created_at
 * @property string $updated_at
 *
 * @method static OrderAutopay whereOrderId()
 * @method static OrderAutopay whereCustomerId()
 * @method static OrderAutopay whereState()
 * @method static OrderAutopay first()
 * @method static OrderAutopay find()
 * @method static OrderAutopay get()
 * @method static OrderAutopay[] all()
 */
class OrderAutopay extends \Eloquent
{
    public $table = 'order_autopay';

    const C_STATE_NEW = 0;
    const C_STATE_PAID = 1;

    public static function getLastPay($customerId, $orderId)
    {
        $result = [
            'lastPay' => null,
            'total' => 0
        ];
        $pays =  self::whereCustomerId($customerId)
            ->whereOrderId($orderId)
            ->whereState(self::C_STATE_NEW)
            ->get();
        $count = count($pays);
        if ($count > 0) {
            $result['total'] = $count;
            $result['lastPay'] = $pays[$count - 1];
        }

        return $result;
    }
}