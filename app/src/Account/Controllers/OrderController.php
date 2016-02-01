<?php


namespace Dryharder\Account\Controllers;


use Dryharder\Agbis\Api;
use Dryharder\Components\Customer;
use Dryharder\Components\Mailer;
use Dryharder\Components\TraitController;
use Dryharder\Components\Validation;
use Dryharder\Models\OrderRequest;
use Input;

class OrderController extends BaseController
{

    use TraitController;

    public function form()
    {


        $user = null;

        try {

            $api = new Api();
            if ($api->key()) {
                $user = $api->ContrInfo();
            }

        } catch (\Exception $e) {

        }

        if ($user) {
            return $this->formByUser($user);
        }

        return $this->formAsGuest();

    }

    private function formByUser($user)
    {
        return \View::make('ac::inc.create-order', [
            'user'    => $user,
            'account' => (bool)\Input::get('account'),
        ]);
    }

    private function formAsGuest()
    {
        return \View::make('ac::inc.create-order-new', [
            'account' => (bool)\Input::get('account'),
        ]);
    }


    public function request()
    {

        $api = new Api();
        $data = [];
        if ($api->key()) {
            try {
                $data = $api->ContrInfo();
            } catch (\Exception $e) {
            }
        }

        $rules = [
            'address1'  => 'required',
            'address2'  => 'required',
            'orderText' => 'required',
            'comment'   => '',
        ];

        // тип формы "новый клиент" (иначе - авторизованный)
        if (!$api->key()) {
            Validation::prepareInput(['phone']);
            $rules['phone'] = 'required|phone';
            $rules['email'] = 'required|email';
        }

        Input::merge(array_map('trim', Input::all()));
        $validator = \Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return $this->responseError($validator, $validator->errors()->first());
        }

        $fields = array_keys($rules);
        foreach ($fields as $key) {
            $data[$key] = Input::get($key);
        }
        Mailer::requestOrderMessage($data);

        OrderRequest::unguard();
        OrderRequest::create([
            'email'     => $data['email'],
            'phone'     => Customer::instance()->phone($data['phone']),
            'address1'  => $data['address1'],
            'address2'  => $data['address2'],
            'orderText' => $data['orderText'],
            'comment'   => $data['comment'],
        ]);

        return $this->responseSuccess([
            'email'   => $data['email'],
            'phone'   => Customer::instance()->phone($data['phone']),
            'address' => $data['address1'],
        ], 'Заказ оформлен');

    }


}