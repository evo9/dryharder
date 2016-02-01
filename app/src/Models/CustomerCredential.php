<?php


namespace Dryharder\Models;

/**
 *
 * @property integer $customer_id
 * @property string  $password
 * @property string  $agbis_password
 *
 */
class CustomerCredential extends \Eloquent
{

    protected $table = 'customer_credentials';
    protected $primaryKey = 'customer_id';

}