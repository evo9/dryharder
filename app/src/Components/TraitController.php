<?php


namespace Dryharder\Components;


use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;
use Response;

trait TraitController {



    /**
     * @param Validator|MessageBag|array $errors
     * @param string                     $message
     * @param integer                    $code
     *
     * @return Response
     */
    protected function responseError($errors, $message = '', $code = 400)
    {

        if ($errors instanceof Validator) {
            $errors = $errors->errors();
        }

        if ($errors instanceof MessageBag) {
            $errors = $errors->toArray();
            foreach ($errors as &$value) {
                $value = $value[0];
            }
        }

        return Response::json([
            'errors' => $errors,
            'message' => $message,
        ], $code);

    }


    protected function responseException(\Exception $exception, $message, $key = '', $code = null)
    {

        $key = $key ? $key : 0;
        $code = $code ? $code : null;
        if (!$code) {
            $code = $exception->getCode() ? $exception->getCode() : null;
        }
        if (!$code) {
            $code = 500;
        }

        return $this->responseError(
            [$key => $exception->getMessage()],
            $message,
            $code
        );

    }

    protected function responseSuccess($data = [], $message = '')
    {

        return Response::json([
            'data' => $data,
            'message' => $message,
        ]);

    }
}