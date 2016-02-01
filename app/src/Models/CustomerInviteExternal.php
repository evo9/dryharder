<?php


namespace Dryharder\Models;

/**
 *
 * @property integer $id
 * @property integer $phone
 * @property integer $owner_id
 * @property integer $source_id
 * @property string  $created_at
 * @property string  $updated_at
 *
 * @property CustomerInvite $registered
 * @property Customer $owner
 *
 * @method static CustomerInviteExternal wherePhone($phone)
 * @method static CustomerInviteExternal first()
 * @method static CustomerInviteExternal with($rel)
 * @method static CustomerInviteExternal[] all()
 * @method static CustomerInviteExternal get()
 * @method static CustomerInviteExternal orderBy($column, $direction)
 */
class CustomerInviteExternal extends \Eloquent
{

    public $table = 'customer_invite_externals';
    public static $unguarded = true;


    public function registered(){
        return $this->hasOne(CustomerInvite::class, 'customer_invite_external_id', 'id');
    }

    public function owner(){
        return $this->belongsTo(Customer::class, 'owner_id', 'id');
    }


}