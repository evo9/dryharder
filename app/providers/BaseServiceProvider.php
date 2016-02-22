<?php


use Dryharder\Components\InviteComponent;
use Dryharder\Components\Validation;

class BaseServiceProvider extends Illuminate\Support\ServiceProvider
{

    public function boot()
    {

        View::addNamespace('mailer', __DIR__ . '/../src/Components/views/mailer');
        View::addNamespace('cmd', app_path() . '/commands/views');
        View::addNamespace('socials', app_path('/src/Components/views/socials'));
        View::addNamespace('flash', app_path('src/Components/views/flashes'));
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

        $this->registerCommands();
        $this->routes();

    }

    private function registerCommands()
    {


        $this->app['dh.api.parse.price'] = $this->app->share(function () {
            return new ParsePriceCommand();
        });
        $this->commands('dh.api.parse.price');

        $this->app['dh.content.parse.price'] = $this->app->share(function () {
            return new ParsePriceContentCommand();
        });
        $this->commands('dh.content.parse.price');

        $this->app['dh.commands.example'] = $this->app->share(function () {
            return new \ExampleCommand();
        });
        $this->commands('dh.commands.example');

        $this->app['dh.commands.garbage.sessions'] = $this->app->share(function () {
            return new \GarbageSessionsCommand();
        });
        $this->commands('dh.commands.garbage.sessions');

        $this->app['dh.commands.customers.updater'] = $this->app->share(function () {
            return new \CustomersUpdaterCommand();
        });
        $this->commands('dh.commands.customers.updater');

        $this->app['dh.commands.invite'] = $this->app->share(function () {
            return new \InviteCommand();
        });
        $this->commands('dh.commands.invite');

        $this->app['dh.commands.notify'] = $this->app->share(function () {
            return new \NotifyOrderCommand();
        });
        $this->commands('dh.commands.notify');

        $this->app['dh.orders.autopay'] = $this->app->share(function () {
            return new \OrdersAutopayCommand();
        });
        $this->commands('dh.orders.autopay');

       /* $this->app['dh.orders.autopay'] = $this->app->share(function () {
            return new \OrdersAutopayCommand();
        });
        $this->commands('dh.orders.autopay');*/

    }

    private function routes()
    {
        // сохраняем куку кода приглашения и редиректим на главную
        Route::get('/welcome/{lang}/{code}.html', function ($lang, $code) {
            $invite = new InviteComponent();
            $lang = in_array($lang, ['ru', 'en']) ? $lang : 'ru';
            App::setLocale($lang);

            return $invite->storeEntry($code);
        });
        Route::get('/welcome/{code}.html', function ($code) {
            $invite = new InviteComponent();

            return $invite->storeEntry($code);
        });

    }


}