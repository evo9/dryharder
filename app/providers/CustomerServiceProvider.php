<?php

use Dryharder\Account\Controllers\OrderController;
use Dryharder\Api\Customer\Controllers\CustomerController;
use Dryharder\Api\Customer\Controllers\SignupController;

class CustomerServiceProvider extends Illuminate\Support\ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->routes();
        header('Access-Control-Allow-Origin: ' . Config::get('agbis.api.cross'));
        header('Access-Control-Allow-Credentials: true');
    }


    private function routes()
    {

        Route::group([
            'prefix' => 'api/v1/customer',
        ], function(){

            Route::any('', CustomerController::class.'@show');

            Route::post('signup/sms/repeat', SignupController::class.'@repeatSms');
            Route::post('signup/register', SignupController::class.'@register');
            Route::post('signup/login', SignupController::class.'@login');
            Route::post('signup/reset', SignupController::class.'@reset');
            Route::post('signup/logout', SignupController::class.'@logout');
            Route::get('signup/promo', SignupController::class.'@promo');

            Route::post('request', OrderController::class.'@request');
            Route::get('request/form', OrderController::class.'@form');

            Route::post('order/message', SignupController::class.'@orderMessage');

        });

    }
}