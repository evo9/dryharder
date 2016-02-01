<?php


namespace Dryharder\Components;


use Config;
use Cookie;
use Dryharder\Models\Address;
use Dryharder\Models\Customer as CM;
use Dryharder\Models\CustomerAddress;
use Dryharder\Models\CustomerCredential;
use Dryharder\Models\PromoCode;
use Dryharder\Models\PromoCodeAddress;
use Session;

class Customer
{


    private static $instance;

    /**
     * @var CM
     */
    private $customer;

    /**
     * @return Customer
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return CM
     */
    public function get()
    {
        return $this->customer;
    }

    /**
     * @param string $phone
     *
     * @return Customer|null
     */
    public function initByPhone($phone)
    {

        $this->customer = CM::wherePhone($this->phone($phone))->first();

        return $this->customer ? $this : null;

    }

    /**
     * @param $id
     *
     * @return Customer|null
     */
    public function initByExternalId($id)
    {
        $this->customer = CM::whereAgbisId($id)->first();

        return $this->customer ? $this : null;
    }

    public function phone($str)
    {
        return ltrim($str, '+78');
    }

    /**
     * @param $password
     *
     * @return bool
     */
    public function checkPassword($password)
    {

        return $this->customer->credential->password === $this->hash($password);

    }

    /**
     * @param string $password
     *
     * @return Customer $this
     */
    public function doChangePassword($password)
    {

        Mailer::newPasswordChanged($this->customer, $password);
        $this->customer->credential->password = $this->hash($password);
        $this->customer->credential->save();
        return $this;

    }

    /**
     * смена пароля агбиса и одновременно собственного api
     *
     * @param string $password
     *
     * @param string $selfPassword
     *
     * @return Customer $this
     */
    public function doChangePasswordSoft($password, $selfPassword = null)
    {

        $selfPassword = $selfPassword ? $selfPassword :  $password;

        $this->customer->credential->password = $this->hash($selfPassword);
        $this->customer->credential->agbis_password = $password;
        $this->customer->credential->save();
        return $this;

    }

    /**
     * хеш пароля
     *
     * @param $str
     *
     * @return string
     */
    private function hash($str)
    {
        return sha1(Config::get('app.key') . $str);
    }


    /**
     * начать новую сессию авторизованного пользователя
     *
     * @return string
     */
    public function startSession()
    {

        $path = 'customer.credentials';
        $key = md5(microtime() . $this->customer->id . $path . time() . mt_rand(12345, 98765));

        Session::put($path . '.id', $this->customer->id);
        Session::put($path . '.key', $key);

        return $key;

    }

    /**
     * внешний пароль агбиса
     *
     * @return mixed
     */
    public function getExternalPassword()
    {
        return $this->customer->credential->agbis_password;
    }

    /**
     * создание нового пользователя в наше базе
     *
     * @param integer $external_id внешней id
     * @param string  $phone       телефон
     *
     * @return Customer
     */
    public function createCustomerByExternal($external_id, $phone)
    {

        Reporter::customerCreateExternalStart($external_id, $phone);

        $item = new CM();

        $item->agbis_id = $external_id;
        $item->phone = $this->phone($phone);
        $item->save_card = 1;
        $item->save();

        $credential = new CustomerCredential();
        $credential->customer_id = $item->id;
        $credential->save();

        Reporter::customerCreateExternalEnd($item->id);

        $this->customer = $item;

        $key = 'customer.first_login';
        Session::put($key, $item->id);

        return $this;

    }

    /**
     * когда обнаружили, что изменились внешние данные пользователя
     *
     * @param string $phone    новый телефон
     * @param string $password новый пароль
     *
     * @return Customer
     */
    public function updateCustomerByExternal($phone, $password)
    {

        Reporter::customerUpdateExternalStart($this->customer->id, $phone, $password);

        $this->customer->phone = $this->phone($phone);
        $this->customer->save();

        $this->customer->credential->agbis_password = $password;
        $this->customer->credential->password = $this->hash($password);
        $this->customer->credential->save();

        Reporter::customerUpdateExternalEnd($this->customer->id);

        return $this;

    }

    /**
     *
     *
     * 'id'           => trim($json->contr_id),
     * 'email'        => trim($json->email),
     * 'name'         => urldecode(trim($json->name)),
     * 'address'      => urldecode(trim($json->address)),
     * 'phone'        => urldecode($phone),
     * 'order_qnt'    => trim($json->order_not_pay),
     * 'orders_total' => trim($json->order_count),
     * 'agree_sms'    => trim($json->agree_to_receive_sms),
     * 'agree_adv'    => trim($json->agree_to_receive_adv_sms),
     * 'confirmed'    => trim($json->access_mail),
     * 'cloud_token'  => trim($json->save_token_pay),
     *
     * 'promo'             => trim($json->promo_code),
     * 'address'           => trim($json->address),
     * 'discount'          => trim($json->discount),
     * 'discount_external' => trim($json->discount_extrnl),
     *
     *
     * @param $info
     */
    public function updateExternalInfo($info)
    {

        // обновляем анкетные данные
        $this->customer->email = $info['email'];
        $this->customer->name = $info['name'];
        $this->customer->phone = $this->phone($info['phone']);
        $this->customer->save();

        // связываем с промокодом и адресом
        $this->setAddress($info['address']);
        if ($info['promo'] && !empty($info['promo']['promo'])) {
            $this->setPromo($info['promo']['promo'], $info['promo']['address']);
        }

    }

    /**
     * связать пользователя с адресом
     * создать адрес, если он не существует
     *
     * @param $addressTitle
     */
    public function setAddress($addressTitle)
    {

        // наличие адреса в основном списке адресов
        $address = Address::where('address', 'LIKE', $addressTitle)->first();
        if (!$address) {
            $address = new Address();
            $address->address = $addressTitle;
            $address->save();
        }

        // наличие этого адреса у пользователя
        $relation = CustomerAddress::whereCustomerId($this->customer->id)
            ->whereAddressId($address->id)
            ->first();

        // связываем пользователя и адрес
        if (!$relation) {
            $relation = new CustomerAddress();
            $relation->address_id = $address->id;
            $relation->customer_id = $this->customer->id;
            $relation->save();
        }

    }

    /**
     * связать пользователя с промокодом, адресом промокода,
     * а промокод с адресом
     * создать промокод и адрес, если они не существуют
     *
     * @param string $promoCode
     * @param string $addressTitle
     */
    public function setPromo($promoCode, $addressTitle)
    {

        $this->setAddress($addressTitle);

        // наличие адреса в основном списке адресов
        $address = Address::where('address', 'LIKE', $addressTitle)->first();

        // наличие промокода в основном списке промокодов
        $promo = PromoCode::where('code', 'LIKE', $promoCode)->first();
        if (!$promo) {
            $promo = new PromoCode();
            $promo->code = $promoCode;
            $promo->save();
        }

        // наличие этого промокода у пользователя
        if ($this->customer->promo_code_id != $promo->id) {
            $this->customer->promo_code_id = $promo->id;
            $this->customer->save();
        }

        // наличие адреса у промо-кода
        $relation = PromoCodeAddress::wherePromoCodeId($promo->id)
            ->whereAddressId($address->id)
            ->first();

        // связываем адрес с промокодом
        if (!$relation) {
            $relation = new PromoCodeAddress();
            $relation->promo_code_id = $promo->id;
            $relation->address_id = $address->id;
            $relation->save();
        }

    }


    /**
     * авторизационный ключ который хранится в сессии
     *
     * @return mixed
     */
    public function key()
    {
        $path = 'customer.credentials';

        return Session::get($path . '.key');
    }

    /**
     * инициализация объекта по сессии
     *
     * @return Customer|null
     */
    public function initByKey()
    {
        $path = 'customer.credentials';
        $id = Session::get($path . '.id');
        $item = CM::find($id);
        if ($item) {
            $this->customer = $item;

            return $this;
        }

        return null;

    }

    /**
     * сброс сессии, удаление авторизационного ключа
     */
    public function cleanup()
    {
        $path = 'customer.credentials';
        Session::remove($path . '.key');
        Session::remove($path . '.id');
    }

    /**
     * запоминать последнюю использованную карту клиента?
     * @return boolean
     */
    public function isSaveCard(){

        return $this->customer->save_card;

    }

    /**
     * оплачивать заказ автоматически?
     * @return boolean
     */
    public function isAutoPay(){

        return $this->customer->auto_pay;

    }


    /**
     * применить настройку "сохранять карту"
     *
     * @param boolean $saveCard
     *
     * @return $this
     */
    public function setSaveCard($saveCard){

        $saveCard = $saveCard ? 1 : 0;
        $this->customer->save_card = $saveCard;
        $this->customer->save();
        return $this;

    }

    /**
     * применить настройку "списывать автоматически"
     *
     * @param boolean $autopay
     *
     * @return $this
     */
    public function setAutopay($autopay){

        $autopay = $autopay ? 1 : 0;
        $this->customer->auto_pay = $autopay;
        $this->customer->save();
        return $this;

    }

    public function destroySession()
    {
        Session::flush();
    }

    public function getTemporaryCookie()
    {
        return Cookie::make('tmp', 'pmt', 0);
    }

    public function getForeverCookie()
    {
        return Cookie::forever('tmp', 'pmt');
    }

    public function isRemembered()
    {
        return Cookie::get('tmp') === 'pmt';
    }

    public function closeIfNotMember(){
        if(!$this->isRemembered()){
            $this->destroySession();
        }
    }


    public function renewRegisterAt()
    {
        $this->customer->renewRegisterAt();
    }

}