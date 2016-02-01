<?php


namespace Dryharder\Models;


use Eloquent;


/**
 * Class CustomerFlash
 *
 * @property integer $flash_id
 * @property integer $customer_id
 * @property integer $qnt
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @method static CustomerFlash whereCustomerId($customer_id)
 * @method static CustomerFlash whereFlashId($flash_id)
 * @method static CustomerFlash first()
 *
 * @package Dryharder\Models
 */
class CustomerFlash extends Eloquent {

    protected $table = 'customer_flashes';

    public static function findLast($flash_id, $customer_id)
    {
        return self::whereCustomerId($customer_id)->whereFlashId($flash_id)->first();
    }


}