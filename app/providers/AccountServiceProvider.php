<?php


use Dryharder\Account\Controllers\MainController;
use Dryharder\Account\Controllers\ServiceController;
use Dryharder\Components\Validation;

class AccountServiceProvider extends Illuminate\Support\ServiceProvider
{

    public function boot()
    {

        View::addNamespace('ac', __DIR__ . '/../src/Account/views');
        View::addExtension('html', 'php');
        Validation::extend();

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->registerRoutes();

    }

    private function registerRoutes()
    {

        Route::get('/account', MainController::class . '@index');
        Route::get('/account/forms/fields', MainController::class . '@forms');
        Route::post('/account/forms/user', MainController::class . '@formUser');
        Route::post('/account/forms/password', MainController::class . '@formPassword');
        Route::post('/account/forms/card/save', MainController::class . '@formCardSave');
        Route::post('/account/forms/autopay/save', MainController::class . '@formAutopaySave');
        Route::post('/account/forms/card/remove', MainController::class . '@formCardRemove');
        Route::get('/account/orders', MainController::class . '@orders');
        Route::get('/account/history', MainController::class . '@history');
        Route::get('/account/order/{id}', MainController::class . '@order');
        Route::get('/account/order/services/{id}', MainController::class . '@orderServices');
        Route::get('/account/order/services/pdf/{id}', MainController::class . '@orderServicesPdf');
        Route::get('/account/pay/init/{id}/{target}/{reset}', MainController::class . '@pay');
        Route::get('/account/new_card', MainController::class . '@newCard');
        Route::post('/account/delete_card', MainController::class . '@deleteCard');
        Route::post('/account/autopay', MainController::class . '@autopay');
        Route::post('/account/pay_finish', MainController::class . '@payFinish');
        Route::get('/account/pay/card', MainController::class . '@card');
        Route::get('/account/prepayment', MainController::class . '@prepayment');
        Route::post('/account/pay_by_token', MainController::class . '@payByToken');
        Route::get('/account/pay/check/{id}', MainController::class . '@checkPay');
        Route::post('/account/pay/token', MainController::class . '@token');
        Route::get('/account/flash/message/{type}', ServiceController::class . '@flash');
        Route::get('/account/bonus', MainController::class . '@bonus');
        Route::get('/account/order/review/{id}', MainController::class . '@review');
        Route::post('/account/order/review', MainController::class . '@reviewOrder');
        Route::get('/account/subscriptions', MainController::class . '@subscriptions');
        Route::get(
            '/account/lang/{lang}',
            [
                'as'     => 'lang.set',
                'before' => 'lang.set',
                function () {
                    return App::getLocale();
                }
            ]
        );

    }


}