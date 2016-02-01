<?php

use Dryharder\Gateway\Controllers\PaymentCloudController;
use Dryharder\Gateway\Controllers\PaymentYamController;

class GatewayServiceProvider extends Illuminate\Support\ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->routes();
    }

    private function routes()
    {

        Route::group([
            'prefix' => 'gateway',
        ], function () {
            Route::any('cloud/check', PaymentCloudController::class . '@check');
            Route::any('cloud/pay', PaymentCloudController::class . '@pay');
            Route::any('cloud/fail', PaymentCloudController::class . '@fail');
            Route::get('cloud/waiting/{id}', PaymentCloudController::class . '@waiting');
            Route::get('cloud/export', PaymentCloudController::class . '@export');
        });

        Route::group([
            'prefix' => 'payment/ya',
        ], function () {
            Route::any('check', PaymentYamController::class . '@check');
            Route::any('aviso', PaymentYamController::class . '@pay');
            Route::any('fail', PaymentYamController::class . '@fail');
        });

    }

}