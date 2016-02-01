<?php

namespace Dryharder\Api;

use Controller;
use Dryharder\Agbis\Api;
use Dryharder\Agbis\ApiException;
use Dryharder\Components\Customer;
use Dryharder\Components\Reporter;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;
use Input;
use Response;

class BaseController extends Controller
{



    protected function getCustomer()
    {

        $api = new Api();
        Customer::instance()->closeIfNotMember();

        // ключ из запроса
        $keyRequest = Input::get('key');

        // наш ключ клиента
        $keyCustomer = Customer::instance()->key();

        // id сессии агбиса
        $keyAgbis = $api->key();

        // при наличии нашего ключа плюс ключа агбиса
        // это значит что мы держим вполне живую сессию
        if ($keyCustomer && $keyAgbis) {
            $customer = Customer::instance()->initByKey();
            Reporter::customerTouchKey($keyCustomer, $keyAgbis, $customer->get()->agbis_id);

            return $customer;
        }

        Reporter::customerEmptyKey($keyCustomer, $keyAgbis);

        // нет своего ключа но есть ключ агбис или ключ запроса (он же ключ агбис)
        // это значит что свой ключ мы еще не выдали по какой то причине

        // поэтому работаем по ключу агбиса (из запроса - приоритетнее)
        Reporter::customerTouchExternalKey($keyRequest, $keyAgbis);
        $keyAgbis = $keyRequest ? $keyRequest : $keyAgbis;

        if ($keyAgbis) {
            try {

                $user = $api->_cache_customer_info($keyAgbis);
                $api->memory((object)[
                    'id'  => $user['id'],
                    'key' => $keyAgbis,
                ]);

                // получили человека по агбису, найдем его у нас и создадим свою сессию
                $customer = Customer::instance()->initByExternalId($user['id']);
                if ($customer) {
                    $key = $customer->startSession();
                    Reporter::loginNewKey($customer->get()->agbis_id, $key);

                    return $customer;
                }

                // иначе это значит, что в нашей базе пользователя нет
                // придется его выбросить и пусть авторизуется заново
                Reporter::customerLostExternalKey($keyAgbis);

            } catch (ApiException $e) {
                Reporter::customerFailExternalKey($keyAgbis);
            }
        } else {
            Reporter::customerEmptyExternalKey();
        }

        Customer::instance()->destroySession();

        return null;

    }

}