<?php

namespace Dryharder\Components;

use Config;
use Dryharder\Gateway\Models\PaymentCloud;
use Illuminate\Mail\Message;
use Mail;

class Mailer
{

    public static function feedbackMessage($data)
    {

        $data['infobox'] = Config::get('mail.infobox');

        $data['subject'] = 'Новое обращение с сайта';
        Mail::send('mailer::feedback.message', $data, function (Message $message) use ($data) {
            $message
                ->from($data['infobox'], Config::get('mail.name'))
                ->to($data['infobox'], Config::get('mail.name'))
                ->replyTo($data['email'], $data['name'])
                ->subject($data['subject']);
        });

    }

    public static function requestOrderMessage($data)
    {

        $data['name'] = empty($data['name']) ? 'Новый клиент (' . $data['email'] . ')' : $data['name'];
        $data['infobox'] = Config::get('mail.infobox');

        $data['subject'] = 'Новая заявка из личного кабинета';
        Mail::send('mailer::customer.request.message', $data, function (Message $message) use ($data) {
            $message
                ->from($data['email'], $data['name'])
                ->to($data['infobox'], Config::get('mail.name'))
                ->replyTo($data['email'], $data['name'])
                ->subject($data['subject']);
        });

    }

    public static function newPasswordChanged($customer, $password)
    {

        $data['email'] = $customer->email;
        $data['name'] = $customer->name;
        $data['password'] = $password;
        $data['infobox'] = Config::get('mail.infobox');

        $data['subject'] = 'Ваш пароль был изменен';
        Mail::send('mailer::customer.account.password', $data, function (Message $message) use ($data) {
            $message
                ->from($data['infobox'], Config::get('mail.name'))
                ->to($data['email'], $data['name'])
                ->subject($data['subject']);
        });

    }

    public static function inviteIsPayment($customer, $owner)
    {

        $data = [
            'infobox'  => Config::get('mail.infobox'),
            'self'     => Config::get('mail.self'),
            'subject'  => 'Сработало приглашение Друга',
            'customer' => $customer,
            'owner'    => $owner,
        ];

        Mail::send('mailer::customer.account.invite', $data, function (Message $message) use ($data) {
            $message
                ->from($data['self'], Config::get('mail.name'))
                ->to($data['infobox'], Config::get('mail.name'))
                ->subject($data['subject']);
        });

    }

    public static function orderReview($data)
    {

        $data['self'] = Config::get('mail.self');
        $data['infobox'] = Config::get('mail.infobox');
        $data['subject'] = $data['order'] > 0
            ? 'Получен новый отзыв о заказе ' . $data['doc_number']
            : 'Получен новый отзыв о заявке ' . $data['doc_number'];

        Mail::send('mailer::feedback.order', $data, function (Message $message) use ($data) {
            $message
                ->from($data['self'], Config::get('mail.name'))
                ->to($data['infobox'], Config::get('mail.name'))
                ->subject($data['subject']);
        });

    }

    public static function confirmOrderMessage($phone, $title)
    {

        $data = [
            'title'   => $title,
            'phone'   => $phone,
            'infobox' => Config::get('mail.infobox'),
            'self'    => Config::get('mail.self'),
            'subject' => 'Пришло подтверждение заказа (' . $phone . ') (' . $title . ')',
        ];

        Mail::send('mailer::customer.account.order-message', $data, function (Message $message) use ($data) {

            \Log::debug('данные для отправки письма', [$data]);

            $message
                ->from($data['self'], Config::get('mail.name'))
                ->to($data['infobox'], Config::get('mail.name'))
                ->subject($data['subject']);
        });

    }

    public static function notifyNewOrder($order, $services, $email, $name, $attach)
    {

        $data = [
            'infobox'  => Config::get('mail.infobox'),
            'order'    => $order,
            'services' => $services,
            'email'    => $email,
            'name'     => $name,
            'subject'  => 'В вашем личном кабинете размещен заказ No ' . $order['doc_number'],
            'attach'   => $attach,
        ];

        Mail::send('mailer::customer.account.order', $data, function (Message $message) use ($data) {
            $message
                ->from($data['infobox'], Config::get('mail.name'))
                ->to($data['email'], $data['name'])
                ->attach($data['attach'])
                ->subject($data['subject']);
        });

    }

    /**
     * @param Customer $customer
     * @param array $order
     * @param PaymentCloud $token
     */
    public static function succesAutoPay($customer, $order, $token)
    {

        $data = [
            'infobox' => Config::get('mail.infobox'),
            'number'  => $order['doc_number'],
            'amount'  => $order['amount'],
            'email'   => $customer->get()->email,
            'name'    => $customer->get()->name,
            'subject' => 'Автоплатеж с вашей карты по заказу номер ' . $order['doc_number'],
            'pan'     => '...' . substr($token->card_pan, -4),
            'type'    => $token->card_type,
        ];

        Mail::send('mailer::customer.account.autopay_success', $data, function (Message $message) use ($data) {
            $message
                ->from($data['infobox'], Config::get('mail.name'))
                ->to($data['email'], $data['name'])
                ->subject($data['subject']);
        });

    }

    /**
     * @param Customer $customer
     * @param array $order
     * @param PaymentCloud $token
     */
    public static function errorAutoPay($customer, $order, $token)
    {

        $data = [
            'infobox' => Config::get('mail.infobox'),
            'number'  => $order['doc_number'],
            'amount'  => $order['amount'],
            'email'   => $customer->get()->email,
            'name'    => $customer->get()->name,
            'subject' => 'Ошибка автоплатежа с вашей карты по заказу номер ' . $order['doc_number'],
            'pan'     => '...' . substr($token->card_pan, -4),
            'type'    => $token->card_type,
        ];

        Mail::send('mailer::customer.account.autopay_error', $data, function (Message $message) use ($data) {
            $message
                ->from($data['infobox'], Config::get('mail.name'))
                ->to($data['email'], $data['name'])
                ->subject($data['subject']);
        });

    }

} 