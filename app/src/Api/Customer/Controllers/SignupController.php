<?php

namespace Dryharder\Api\Customer\Controllers;

use Dryharder\Agbis\Api;
use Dryharder\Agbis\ApiException;
use Dryharder\Api\BaseController;
use Dryharder\Components\Customer;
use Dryharder\Components\InviteComponent;
use Dryharder\Components\Mailer;
use Dryharder\Components\Reporter;
use Dryharder\Components\TraitController;
use Dryharder\Components\Validation;
use Input;

class SignupController extends BaseController
{

    use TraitController;

    public function orderMessage()
    {
        $title = Input::get('title');

        Validation::prepareInput(['phone']);
        $phone = Input::get('phone');
        $validator = Validation::validator(['phone']);

        if($validator->fails()){
            return $this->responseError($validator, 'Некорректные данные');
        }

        $user = Customer::instance()->initByPhone($phone);
        if($user){
            Mailer::confirmOrderMessage($phone, $title);
            return $this->responseSuccess([], 'Спасибо! Подтверждение принято.');
        }

        return $this->responseError([], 'К сожалению, такой номер не зарегистрирован. ' .
            'Пожалуйста, пройдите регистрацию на сайте dryharder.me прежде, ' .
            'чем оставлять заказ');

    }

    /**
     * повтор отправки смс с паролем после регистрации
     * @return \Response
     */
    public function repeatSms()
    {
        Validation::prepareInput(['phone']);
        $phone = Input::get('phone');
        $validator = Validation::validator(['phone']);

        if($validator->fails()){
            return $this->responseError($validator, 'Некорректные данные');
        }

        try{

            $api = new Api();
            $message = $api->ResubmitSms($phone);
            return $this->responseSuccess([], $message);

        }catch(\Exception $e){
            return $this->responseException($e, 'Не удалось повторить отправку пароля по смс');
        }

    }

    /**
     * сброс пароля
     * @return \Response
     */
    public function reset()
    {
        Validation::prepareInput(['phone']);
        $phone = \Input::get('phone');
        $validator = Validation::validator(['phone']);

        if($validator->fails()){
            return $this->responseError($validator, 'Некорректные данные');
        }

        try{

            $api = new Api();
            $response = $api->Remember_pas($phone);
            if(!empty($response->pwd)){
                Customer::instance()->initByPhone($phone)->doChangePasswordSoft($response->pwd);
            }
            return $this->responseSuccess([], $response->Msg);

        }catch(\Exception $e){
            return $this->responseException($e, 'Не удалось повторить отправку пароля по смс');
        }

    }

    /**
     * логаут
     * @return \Response
     */
    public function logout()
    {

        $api = new Api();
        $key = Input::get('key');
        if(!$key){
            $key = $api->key();
        }

        try{

            $api->Logout($key);
            return $this->responseSuccess([], 'Сессия остановлена');

        }catch(\Exception $e){
            return $this->responseException($e, 'Ошибка удаления сессии');
        }

    }

    /**
     * авторизация
     * @return \Response
     */
    public function login()
    {
        Validation::prepareInput(['phone']);
        $phone = \Input::get('phone');
        $password = \Input::get('password');
        $selfPassword = null;
        $validator = Validation::validator(['phone', 'password']);

        if($validator->fails()){
            Reporter::loginFailValidate($validator);
            return $this->responseError($validator, 'Некорректные данные');
        }
        Reporter::loginStart($phone, $password);

        // ключ который мы должны в итоге выдать фронтенду
        $key = null;

        // находим у себя
        $customer = Customer::instance()->initByPhone($phone);
        if ($customer) {
            Reporter::loginFoundSelf($customer->get()->agbis_id);

            // авторизуем по своему паролю
            if ($customer->checkPassword($password)) {
                Reporter::loginPasswordSelf($customer->get()->agbis_id);
                // наш пароль
                $selfPassword = $password;
                // в агбис будем входить по внешнему паролю
                $password = $customer->getExternalPassword();
            }
        }

        // получаем сессию агбиса
        try{

            $api = new Api();
            $user = $api->Login_con($phone, $password);
            $api->memory($user);

            // запомним отдельно в сессии api
            if (Input::get('remember')) {
                $cookie = Customer::instance()->getForeverCookie();
            } else {
                $cookie = Customer::instance()->getTemporaryCookie();
            }

            Reporter::loginExternal($user->id, $phone);

            // для нас - новый человек
            if (!$customer) {

                // пробуем найти по id агбиса
                $customer = Customer::instance()->initByExternalId($user->id);
                if ($customer) {
                    // если нашли, это значит изменился телефон, обновим всю информацию
                    $customer->updateCustomerByExternal($phone, $password);
                } else {
                    // создадим нового
                    $customer = Customer::instance()->createCustomerByExternal($user->id, $phone, $password);
                }

            }

            // устанавливаем пароль в соответствии с успешным входом
            $customer->doChangePasswordSoft($password, $selfPassword);

            // по итогу, у нас есть "наш" человек
            // и мы начинам свою сессию
            $key = $customer->startSession();
            Reporter::loginNewKey($customer->get()->id, $key);

            $invite = new InviteComponent();
            $invite->registerInvite($customer->get());

            // обновляем дату-время последнего входа
            $customer->renewRegisterAt();

            // и именно наш ключ отдаем клиенту
            $response = $this->responseSuccess(['key' => $key], 'Успешная авторизация')->withCookie($cookie);
            return $response;

        }catch(\Exception $e){
            Reporter::loginFailException($e);
            return $this->responseException($e, 'Ошибка авторизации', null, 401);
        }

    }


    /**
     * регистрация
     * @return \Response
     */
    public function register()
    {
        Validation::prepareInput(['phone']);

        // всегда обязательные поля
        $fields = ['name', 'phone', 'email'];

        // регистрация может быть основна на адресе,
        // который был определен по промокоду
        if(\Input::get('address_id')){
            $fields[] = 'address_id';
        }elseif(\Input::get('address')){
            $fields[] = 'address';
        } else {
            // или при регистрации указывается полный адрес
            $fields[] = 'city';
            $fields[] = 'street';
            $fields[] = 'house';
            $fields[] = 'room';
            $fields[] = 'float';
        }

        $validator = Validation::validator($fields);
        if($validator->fails()){
            return $this->responseError($validator, 'Некорректные данные');
        }

        try{

            $api = new Api();
            $message = $api->RegistrNew(\Input::only($fields));

            // инвайт
            $invite = new InviteComponent();
            $cookie = $invite->registerInviteExternal($validator->getData()['phone']);

            $response = $this->responseSuccess([], $message);
            if($cookie){
                $response->withCookie($cookie);
            }

            return $response;

        }catch(\Exception $e){
            return $this->responseException($e, 'Не удалось обработать данные');
        }

    }

    /**
     * поиск адресов по промо-коду
     */
    public function promo(){

        $promo = Input::get('promo');
        if(!$promo){
            return $this->responseSuccess([], 'нет значения для поиска адресов');
        }

        $api = new Api();

        try {
            $list = $api->PromoCode($promo);
        }catch (ApiException $e){
            return $this->responseSuccess([], $e->getMessage());
        }

        if(!$list){
            return $this->responseSuccess([], 'адреса не найдены');
        }

        return $this->responseSuccess([
            'addresses' => $list,
        ], 'найдены адреса');

    }


} 