<?php


namespace Dryharder\Components\Payment;


class CloudPaymentException extends \Exception {

    public function __construct($message, $code = 500){

        \Log::debug('throw exception with CloudPaymentException', [
            'message' => $message,
            'code' => $code,
        ]);

        parent::__construct($message, $code);

    }

}