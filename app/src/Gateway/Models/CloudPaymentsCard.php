<?php


namespace Dryharder\Gateway\Models;

/**
 * Class CloudPaymentsCard
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $payment_id
 * @property float   $amount
 * @property string  $created_at
 * @property string  $updated_at
 * @property string  $external_at
 * @property string  $token
 * @property string  $card_pan
 * @property string  $card_type
 * @property string  $ip
 * @property string  $email
 * @property boolean $is_default
 *
 * @method static CloudPaymentsCard whereCustomerId($customer_id)
 * @method static CloudPaymentsCard whereCardPan($card_pan)
 * @method static CloudPaymentsCard first()
 *
 */
class CloudPaymentsCard extends \Eloquent
{

    protected $table = 'cloud_payments_card';

    public static function whereCustomerCard($card_pan, $customer_id)
    {
        return self::whereCardPan($card_pan)
            ->whereCustomerId($customer_id)
            ->first();
    }

}