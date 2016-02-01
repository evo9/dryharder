<?php


namespace Dryharder\Models;

/**
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $customer_invite_external_id
 * @property integer $owner_id
 * @property integer $source_id
 * @property string  $created_at
 * @property string  $updated_at
 * @property integer $bonus
 *
 * @property Customer $owner
 * @property Customer customer
 *
 * @method static CustomerInvite whereBonus($boolean)
 * @method static CustomerInvite get()
 * @method static CustomerInvite[] all()
 */
class CustomerInvite extends \Eloquent
{

    public $table = 'customer_invites';
    public static $unguarded = true;

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function owner(){
        return $this->belongsTo(Customer::class, 'owner_id', 'id');
    }

}