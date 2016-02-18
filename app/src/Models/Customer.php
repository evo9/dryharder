<?php


namespace Dryharder\Models;

use DB;

use Dryharder\Gateway\Models\PaymentCloud;

/**
 *
 * @property integer            $id
 * @property integer            $agbis_id
 * @property integer            $promo_code_id
 * @property string             $phone
 * @property string             $name
 * @property string             $email
 * @property string             $invite
 * @property string             $auth_at
 * @property CustomerCredential $credential
 * @property CustomerCredential $save_card
 * @property CustomerCredential $auto_pay
 *
 * @method static Customer whereEmail($email)
 * @method static Customer whereInvite($code)
 * @method static Customer wherePhone($phone)
 * @method static Customer whereAgbisId($id)
 * @method static Customer getAutopayAll()
 * @method static Customer first()
 * @method static Customer get()
 * @method static Customer[] all()
 *
 */
class Customer extends \Eloquent
{

    protected $table = 'customers';
    public $existsPaid = null;
    public $tokenExists = null;

    public function credential()
    {
        return $this->hasOne(CustomerCredential::class, 'customer_id', 'id');
    }

    /**
     * создает код для инвайта
     *
     * @return string
     */
    private function initInvite()
    {

        $code = str_random(6);
        while (self::whereInvite($code)->exists()) {
            $code = str_random(6);
        }

        $this->invite = $code;
        $this->save();

        return $this->invite;

    }

    /**
     * код для инвайта
     *
     * @return string
     */
    public function getInviteCode()
    {

        if (!$this->invite) {
            $this->initInvite();
        }

        return $this->invite;

    }

    /**
     * еще не было входа?
     *
     * @return bool
     */
    public function isEmptyAuthAt()
    {
        return (
            empty($this->auth_at) ||
            (int)$this->auth_at == 0 ||
            $this->auth_at == '0000-00-00 00:00:00'
        );
    }

    /**
     * обновление даты последнего входа
     */
    public function renewRegisterAt()
    {
        $this->auth_at = date('Y-m-d H:i:s');
        $this->save();
    }

    public function initExistsPaid()
    {
        $this->existsPaid = PaymentCloud::whereCustomerId($this->agbis_id)->payed()->exists();
        return $this->existsPaid;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['existsPaid'] = $this->existsPaid;
        $array['tokenExists'] = $this->tokenExists;
        return $array;
    }

    public static function scopeGetAutopayAll()
    {
        $customers = Customer::leftJoin('payment_cloud', function($join) {
                $join->on('payment_cloud.customer_id', '=', 'customers.agbis_id');
            })
            ->where('payment_cloud.autopay', '=', 1)
            ->get();

        return $customers;
    }

}