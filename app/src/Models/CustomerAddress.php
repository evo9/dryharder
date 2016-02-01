<?php


namespace Dryharder\Models;


/**
 *
 * @property integer $customer_id
 * @property integer $address_id
 *
 * @method static CustomerAddress whereCustomerId($customer_id)
 * @method static CustomerAddress whereAddressId($address_id)
 * @method static CustomerAddress first()
 */
class CustomerAddress extends \Eloquent
{

    protected $table = 'customer_addresses';
    public $timestamps = false;

}