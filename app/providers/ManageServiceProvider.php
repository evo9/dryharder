<?php


use Dryharder\Manage\Controllers\AutoPayController;
use Dryharder\Manage\Controllers\ContentBlockController;
use Dryharder\Manage\Controllers\OrderRequestController;
use Dryharder\Manage\Controllers\ReportController;
use Dryharder\Manage\Controllers\ServiceTitleController;

class ManageServiceProvider extends Illuminate\Support\ServiceProvider {

    public function boot(){

        View::addNamespace('man', __DIR__ . '/../src/Manage/Views');
        View::addExtension('html', 'php');

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->registerCommands();

    }

    private function registerCommands()
    {

        Route::group(
            [
                'prefix' => 'man',
                'before' => 'manage.auth.basic'
            ],
            function(){

                Route::any('content/blocks', ContentBlockController::class . '@index');
                Route::post('content/blocks/store', ContentBlockController::class . '@store');
                Route::post('content/blocks/{id}', ContentBlockController::class . '@update');
                Route::delete('content/blocks/{id}', ContentBlockController::class . '@delete');

                Route::any('service/titles', ServiceTitleController::class . '@index');
                Route::post('service/titles/{id}', ServiceTitleController::class . '@update');

                Route::get('requests', OrderRequestController::class . '@index');

                Route::any('reporter', ReportController::class . '@index');
                Route::any('reporter/invite/stat', ReportController::class . '@inviteStat');
                Route::any('reporter/reviews', ReportController::class . '@orderReviews');
                Route::any('autopays', AutoPayController::class . '@index');
                Route::any('autopays/orders/{cid}', AutoPayController::class . '@orders');
                Route::any('autopays/start/{order_id}/{customer_id}', AutoPayController::class . '@start');
                Route::any('autopays/check_customers_orders/{customer_id}', AutoPayController::class . '@checkCustomersOrders');
                Route::any('autopays/autopay_all', AutoPayController::class . '@autopayAll');

            }
        );

    }


}