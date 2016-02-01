<?php

namespace Dryharder\Manage\Controllers;

class BaseController extends \Controller{


    protected function isAcceptedJson(){
        return (in_array('application/json', \Request::getAcceptableContentTypes()));
    }

}