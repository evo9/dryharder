<?php


namespace Dryharder\Components;


use Input;
use Validator;

class Validation
{


    public static $rules = [
        'phone'      => 'required|phone',
        'phone2'     => 'required|phone',
        'email'      => 'required|email',
        'name'       => 'required',
        'address'    => 'required',
        'address_id' => '',
        'city'       => 'required',
        'street'     => 'required',
        'house'      => 'required',
        'room'       => 'required',
        'float'      => '',
        'password'   => 'required',
    ];

    public static $messages = [
        'phone'    => [
            'required' => 'Введите номер мобильного телефона',
            'phone'    => 'Проверьте правильность ввода номера',
        ],
        'phone2'   => [
            'required' => 'Введите номер мобильного телефона',
            'phone'    => 'Проверьте правильность ввода номера',
        ],
        'email'    => [
            'required' => 'Обязательно заполните поле email',
            'email'    => 'Проверьте правильность ввода email',
        ],
        'name'     => [
            'required' => 'Заполните поле с фамилией и именем',
        ],
        'address'  => [
            'required' => 'Заполните поле с адресом',
        ],
        'city'     => [
            'required' => 'Укажите город, в котором вы хотите получить услугу',
        ],
        'street'   => [
            'required' => 'Нужно название улицы для курьера',
        ],
        'house'    => [
            'required' => 'Укажите номер дома или офисного здания',
        ],
        'room'     => [
            'required' => 'Укажите номер квартиры или офиса',
        ],
        'password' => [
            'required' => 'Требуется указать пароль',
        ]
    ];

    public static function rules($fields)
    {

        $rules = [];
        foreach (self::$rules as $key => $value) {
            if (in_array($key, $fields)) {
                $rules[$key] = $value;
            }
        }

        return $rules;

    }

    public static function messages($fields)
    {

        $messages = [];
        foreach (self::$messages as $key => $rules) {
            if (in_array($key, $fields)) {

                foreach ($rules as $rule => $message) {
                    $messages[$key . '.' . $rule] = $message;
                }

            }
        }

        return $messages;

    }


    public static function validator($fields)
    {

        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make(
            Input::only($fields),
            self::rules($fields),
            self::messages($fields)
        );

        return $validator;

    }


    public static function extend()
    {

        Validator::extend('phone', function ($attribute, $value, $parameters) {
            $value = preg_replace('/[^\d]+/', '', $value);
            if (strlen($value) < 10 || strlen($value) > 12) {
                return false;
            }

            return true;

        });

    }

    /**
     * подчищает данные в Input
     *
     * @param $names
     */
    public static function prepareInput($names)
    {

        foreach ($names as $name) {

            switch ($name) {

                case 'phone':

                    // убираем из телефона лишние символы и +78 слева
                    // потом добавляем +7

                    $phone = Input::get('phone');
                    $phone = trim($phone);
                    $phone = preg_replace('/[^\d]+/', '', $phone);
                    $phone = ltrim($phone, '78');
                    $phone = '+7' . $phone;

                    Input::merge(['phone' => $phone]);

                    break;

            }

        }


    }


}