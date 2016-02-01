<?php

namespace Dryharder\Api\Feedback\Controllers;

use Dryharder\Api\BaseController;
use Dryharder\Components\Mailer;
use Dryharder\Components\TraitController;
use Dryharder\Components\Validation;
use Input;
use Validator;

class MessageController extends BaseController
{

    use TraitController;

    public function create()
    {
        Validation::prepareInput(['phone']);

        $validator = Validator::make(
            Input::all(),
            [
                'name'  => 'required|min:3',
                'phone' => 'required|min:10',
                'email' => 'required|email',
                'text'  => 'required|min:10',
            ]
        );

        if ($validator->fails()) {
            return $this->responseError($validator, 'Исправьте ошибки в полях формы');
        }

        Mailer::feedbackMessage([
            'name'  => Input::get('name'),
            'phone' => Input::get('phone'),
            'email' => Input::get('email'),
            'text'  => Input::get('text'),
        ]);

        return $this->responseSuccess();

    }

} 