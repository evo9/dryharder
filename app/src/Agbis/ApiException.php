<?php


namespace Dryharder\Agbis;


class ApiException extends \Exception {


    const ERROR_DATA = 400;
    const ERROR_SERVER = 500;

    public function __construct($message, $code = 500){

        \Log::debug('throw exception with ApiException', [
            'message' => $message,
            'code' => $code,
        ]);

        parent::__construct($message, $code);

    }


    public function isDataError(){
        return $this->getCode() == self::ERROR_DATA;
    }

    public function isServerError(){
        return $this->getCode() == self::ERROR_SERVER;
    }

}