<?php

namespace Dryharder\Api\Customer\Controllers;

use Dryharder\Agbis\Api;
use Dryharder\Agbis\ApiException;
use Dryharder\Api\BaseController;
use Dryharder\Components\Reporter;
use Dryharder\Components\TraitController;

class CustomerController extends BaseController
{

    use TraitController;

    public function show()
    {

        $customer = $this->getCustomer();
        if (!$customer) {
            return $this->responseError(['Данные не доступны'], 'Требуется авторизация', 401);
        }

        $api = new Api();
        try {

            $key = $api->key();
            Reporter::aggregateExternalInfoStart($key, $api->id(), $customer->get()->id);

            $client = $api->_cache_customer_info($key);
            $client['key'] = $key;

            try {
                $promo = $api->PromoCodeUse($key);
            }catch (ApiException $e){
                if($e->isDataError()){
                    $promo = null;
                }else{
                    throw $e;
                }
            }

            $client['promo'] = $promo;

            Reporter::aggregateExternalInfoEnd($customer->get()->id);

            $customer->updateExternalInfo($client);

            return $this->responseSuccess($client, 'Данные клиента');

        } catch (ApiException $e) {

            $api->cleanup();

            return $this->responseException($e, 'Не удалось получить данные', 'customer');
        }

    }



}