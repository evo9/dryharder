<?php


namespace Dryharder\Models;


use Eloquent;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $order_id
 * @property int $sent
 * @property int $created_at
 *
 * @method static NotifyOrder where()
 * @method static NotifyOrder first()
 * @method static NotifyOrder[] all()
 * @method static NotifyOrder whereCustomerId()
 * @method static NotifyOrder whereOrderId()
 * @method static NotifyOrder whereSent()
 * @method static NotifyOrder whereRaw()
 */
class NotifyOrder extends Eloquent
{

    public $table = 'notify_orders';

}