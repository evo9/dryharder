<?php

namespace Dryharder\Account\Controllers;

use Dryharder\Components\Flash;
use Response;

class ServiceController extends BaseController
{

    public function flash()
    {

        $flash = new Flash();

        $view = $flash->getFlashMessage();
        if(!$view){
            return Response::make('');
        }

        return $view;

    }

}