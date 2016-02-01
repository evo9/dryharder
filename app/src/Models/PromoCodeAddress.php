<?php


namespace Dryharder\Models;

/**
 * @property integer $promo_code_id
 * @property integer $address_id
 * @method static PromoCodeAddress wherePromoCodeId($promo_code_id)
 * @method static PromoCodeAddress whereAddressId($address_id)
 */
class PromoCodeAddress extends \Eloquent
{

    protected $table = 'promo_code_addresses';
    public $timestamps = false;

}