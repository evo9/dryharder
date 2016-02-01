<?php


namespace Dryharder\Models;


use Eloquent;

/**
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property string $description
 * @property integer $customer_id
 * @property integer $order_id
 * @property integer $price
 *
 * @method static Subscription whereCustomerId($customer_id)
 * @method static Subscription[] all()
 * @method static Subscription get()
 * @method static Subscription find()
 *
 * Class Subscription
 *
 * @package Dryharder\Models
 */
class Subscription extends Eloquent {

    public $table = 'subscriptions';

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

}