<?php


namespace Dryharder\Gateway\Models;

/**
 * Class RefundPayment
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $transaction_id
 * @property integer $payment_id
 * @property float   $amount
 * @property string  $created_at
 * @property string  $updated_at
 *
 * @method static PaymentCloud[] all()
 * @method static PaymentCloud get()
 *
 */
class RefundPayment extends \Eloquent
{
    protected $table = 'refund_payment';
}