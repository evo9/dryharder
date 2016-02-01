<?php

use Dryharder\Api\Feedback\Controllers\MessageController;

class FeedBackServiceProvider extends Illuminate\Support\ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->routes();
        header('Access-Control-Allow-Origin: ' . Config::get('agbis.api.cross'));
    }


    private function routes()
    {

        Route::group([
            'prefix' => 'api/v1/feedback',
        ], function(){
            Route::any('message/create', MessageController::class.'@create');
        });

    }
}