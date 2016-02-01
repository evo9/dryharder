<?php


namespace Dryharder\Components;


use Cookie;
use Dryharder\Models\Customer as CM;
use Dryharder\Models\CustomerInvite;
use Dryharder\Models\CustomerInviteExternal;
use Response;

class InviteComponent
{

    const C_LINK = 1;
    const C_EMAIL = 2;


    public function getInviteCode()
    {

        return Customer::instance()->get()->getInviteCode();

    }

    public function findByCode($code)
    {

        return CM::whereInvite($code)->first();

    }

    public function storeEntry($code)
    {
        $cookie = Cookie::forever('dh_invite', $code);
        $content = \View::make('socials::welcome.invite')->render();
        return Response::make($content)->withCookie($cookie);
    }

    public function url()
    {
        $code = $this->getInviteCode();

        $lang = \App::getLocale();
        if($lang == 'ru'){
            $lang = '';
        }else{
            $lang = $lang . '/';
        }
        return url('/welcome/' . $lang . $code . '.html');
    }

    /**
     * @param CM $customer свежезарегистрированный пользователь
     *
     * @return null|\Symfony\Component\HttpFoundation\Cookie
     */
    public function registerInvite(CM $customer)
    {

        // уже осуществлялся вход ранее, не обрабатываем
        if(!$customer->isEmptyAuthAt()){
            return false;
        }

        // при регистрации должен был сохраниться телефон, если был инвайт, проверим
        $external = CustomerInviteExternal::wherePhone($customer->phone)->first();
        if (!$external || empty($external->owner_id)) {
            return false;
        }

        // нельзя приглашать самого себя
        if ($external->owner_id == $customer->id) {
            return false;
        }

        Reporter::inviteCodeFound($customer->id, $external->owner_id);

        $invite = CustomerInvite::create([
            'customer_id'                 => $customer->id,
            'owner_id'                    => $external->owner_id,
            'source_id'                   => $external->source_id,
            'customer_invite_external_id' => $external->id,
        ]);

        Reporter::inviteCodeRegistered($customer->id, $external->owner_id, $invite->id);

        return true;

    }

    /**
     * временная запись инвайта для внешнего id клиента
     *
     * @param string $phone внешний id клиента
     *
     * @return null|\Symfony\Component\HttpFoundation\Cookie
     */
    public function registerInviteExternal($phone)
    {

        $code = Cookie::get('dh_invite');
        $phone = Customer::instance()->phone($phone);

        if (!$code) {
            return null;
        }

        $cookie = Cookie::forget('dh_invite');
        $owner = $this->findByCode($code);

        if (!$owner) {
            return $cookie;
        }

        // нельзя приглашать самого себя
        if ($owner->phone == $phone) {
            return $cookie;
        }

        Reporter::inviteCodeFoundExternal($phone, $owner->id, $code);

        $invite = CustomerInviteExternal::create([
            'phone'     => $phone,
            'owner_id'  => $owner->id,
            'source_id' => self::C_LINK,
        ]);

        Reporter::inviteCodeRegisteredExternal($phone, $owner->id, $invite->id, $code);

        return $cookie;

    }

}