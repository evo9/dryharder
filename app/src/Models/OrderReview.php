<?php


namespace Dryharder\Models;


use Eloquent;

/**
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $text
 * @property integer $stars
 * @property integer $customer_id
 * @property string $doc_number
 * @property integer $order_id
 *
 * @method static OrderReview[] all()
 * @method static OrderReview get()
 * @method static OrderReview whereCustomerId($customer_id)
 * @method static OrderReview whereDocNumber($order_id)
 *
 * Class OrderReviews
 *
 * @package Dryharder\Models
 */
class OrderReview extends Eloquent {

    public $table = 'order_reviews';

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

}