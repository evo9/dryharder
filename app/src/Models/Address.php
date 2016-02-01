<?php


namespace Dryharder\Models;

/**
 * @property integer $id
 * @property string  $address
 *
 * @method static Address where()
 * @method static Address first()
 *
 * Class Address
 */
class Address extends \Eloquent
{

    protected $table = 'addresses';

}