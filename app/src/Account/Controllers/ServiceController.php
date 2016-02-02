<?php

namespace Dryharder\Account\Controllers;

use Dryharder\Components\Flash;
use Response;

class ServiceController extends BaseController
{

    public function flash($type)
    {

        $flash = new Flash($type);

        $view = $flash->getFlashMessage();
        if(!$view){
            return Response::make('');
        }

        return $view;

    }

}