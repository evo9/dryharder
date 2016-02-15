<?php
namespace Dryharder\Agbis;


use Config;
use Dryharder\Components\Customer;
use Dryharder\Components\Reporter;

class Api
{

    public function cleanup()
    {
        \Session::remove('agbis.user.key');
    }

    public function memory($user)
    {
        \Session::put('agbis.user.id', $user->id);
        \Session::put('agbis.user.key', $user->key);
    }

    public function key()
    {
        return \Session::get('agbis.user.key');
    }

    public function id()
    {
        return \Session::get('agbis.user.id');
    }


    public function ResubmitSms($phone)
    {

        $data = [
            'ResubmitSms' => json_encode([
                'Fone' => $phone
            ]),
            'json'        => 'yes',
        ];

        $url = http_build_query($data);
        $message = $this->curl($url, 'json', 'Msg');
        $message = urldecode($message);

        return $message;

    }

    public function Logout($key)
    {

        $data = [
            'Logout'    => '',
            'SessionID' => $key,
            'json'      => 'yes',
        ];

        $url = http_build_query($data);
        $this->curl($url, 'json');

    }


    public function Remember_pas($phone)
    {

        $data = [
            'Remember_pas' => json_encode([
                'fone' => $phone
            ]),
            'json'         => 'yes',
        ];

        $url = http_build_query($data);
        $response = $this->curl($url, 'json');
        $response->Msg = urldecode($response->Msg);

        return $response;

    }


    public function Login_con($phone, $password)
    {

        $data = [
            'Login_con' => json_encode([
                'Fone'     => $phone,
                'Password' => sha1($password)
            ]),
            'json'      => 'yes',
        ];

        $url = http_build_query($data);
        $json = $this->curl($url, 'json');

        return (object)[
            'id'  => (int)$json->id_user,
            'key' => trim($json->Session_id),
        ];

    }


    public function RegistrNew($data)
    {

        $postData = [
            'mail'        => $data['email'],
            'email'       => $data['email'],
            'change_name' => $data['name'],
            'fone'        => $data['phone'],
        ];

        $address = [];
        if (!empty($data['address'])) {
            $address[] = $data['address'];
        }
        if (!empty($data['city'])) {
            $address[] = $data['city'];
        }
        if (!empty($data['street'])) {
            $address[] = $data['street'];
        }
        if (!empty($data['house'])) {
            $address[] = 'д. ' . $data['house'];
        }
        if (!empty($data['room'])) {
            $address[] = 'кв/офис. ' . $data['room'];
        }
        if (!empty($data['float'])) {
            $address[] = 'этаж ' . $data['float'];
        }

        // полный адрес одной строкой
        if (!empty($address)) {
            $postData['address2'] = implode(', ', $address);
        }

        // или id адреса, который был найден по промокоду
        if (!empty($data['address_id'])) {
            $postData['working_address'] = $data['address_id'];
        }

        // запрос в api
        $data = [
            'RegistrNew' => json_encode($postData),
            'json'       => 'yes',
        ];

        $url = http_build_query($data);
        $message = $this->curl($url, 'json', 'Msg');
        $message = urldecode($message);

        return $message;

    }

    public function ContrInfo($key = null)
    {

        if (null === $key) {
            $key = $this->key();
        }

        $json = $this->curl('ContrInfo=&SessionID=' . $key . '&json=yes', 'json');
        $phone = trim($json->fone_cell)
            ? trim($json->fone_cell)
            : trim($json->fone);

        $data = [
            'id'           => trim($json->contr_id),
            'email'        => trim($json->email),
            'name'         => urldecode(trim($json->name)),
            'address'      => urldecode(trim($json->address)),
            'phone'        => urldecode($phone),
            'phone2'       => urldecode($json->fone),
            'order_qnt'    => trim($json->order_not_pay),
            'orders_total' => trim($json->order_count),
            'agree_sms'    => trim($json->agree_to_receive_sms),
            'agree_adv'    => trim($json->agree_to_receive_adv_sms),
            'confirmed'    => trim($json->is_confirmed_email),
            'cloud_token'  => trim($json->save_token_pay),
            'discount'     => trim($json->discount),
        ];

        return $data;

    }

    public function SaveInfo($info)
    {

        $data = [
            'Name'        => $info['name'],
            'Teleph_cell' => $info['phone'], // мобильный
            'Email'       => $info['email'],
            'Address'     => $info['address'],
        ];
        if ($info['phone2']) {
            $data['Fone'] = $info['phone2']; // домашний
        }
        $data = urlencode(json_encode($data, JSON_UNESCAPED_UNICODE));

        $this->curl('SaveInfo=' . $data . '&SessionID=' . $this->key() . '&json=yes', 'json');

    }

    public function PromoCodeUse($key = null)
    {

        if (null === $key) {
            $key = $this->key();
        }

        $json = $this->curl('PromoCodeUse=&SessionID=' . $key . '&json=yes', 'json');

        $data = [
            'promo'             => trim($json->promo_code),
            'address'           => trim(urldecode($json->address)),
            'discount'          => trim($json->discount),
            'discount_external' => trim($json->discount_extrnl),
        ];

        return $data;

    }

    public function Bonus($key = null)
    {
        if (!$key) {
            $key = $this->key();
        }

        $json = $this->curl('Bonus=&SessionID=' . $key . '&json=yes', 'json');

        $data = [
            'bonus' => trim($json->bonus_rest),
        ];

        return $data;

    }

    public function Deposit($key)
    {
        $json = $this->curl('Deposit=&SessionID=' . $key . '&json=yes', 'json');

        $data = [
            'deposit' => trim($json->deposit_rest),
        ];

        return $data;

    }

    /**
     * @param string $key
     *
     * @return array
     * @throws ApiException
     */
    public function Orders($key = null)
    {

        if (null === $key) {
            $key = $this->key();
        }

        $json = $this->curl('Orders=&SessionID=' . $key . '&json=yes', 'json');
        $result = [];

        foreach ($json->orders as $order) {
            $item = [
                'id'              => $order->dor_id,
                'doc_number'      => $order->doc_num,
                'amount_credit'   => $order->kredit,
                'amount_debit'    => $order->debet,
                'date_in'         => $order->doc_date,
                'date_out'        => $order->date_out,
                'status'          => $order->status,
                'photo_exist'     => $order->photo_exist,
                'amount_discount' => $order->discount,
                'amount'          => $order->kredit - $order->debet,
            ];
            $result[] = $item;
        }

        $data = [
            'orders' => $result,
        ];

        return $data;

    }

    public function Services($orderId, $key = null)
    {

        if (null === $key) {
            $key = $this->key();
        }

        $services = urlencode('{"dor_id": ' . $orderId . ', "info": 1}');
        $json = $this->curl('Services=' . $services . '&SessionID=' . $key . '&json=yes', 'json');
        $result = [];

        foreach ($json->order_servises as $service) {
            $item = [
                'name'   => urldecode($service->service),
                'amount' => $service->kredit,
                'status' => $service->status_id,
                'qnt'    => $service->qty_kredit,
            ];
            $result[] = $item;
        }

        $data = [
            'services' => $result,
        ];

        return $data;

    }

    public function OrdersHistory($key = null)
    {

        if (null === $key) {
            $key = $this->key();
        }

        $json = $this->curl('OrdersHistory=&SessionID=' . $key . '&json=yes', 'json');
        $result = [];

        foreach ($json->orders_history as $order) {
            $item = [
                'id'              => $order->dor_id,
                'doc_number'      => $order->doc_num,
                'amount_credit'   => $order->kredit,
                'amount_debit'    => $order->debet,
                'date_in'         => $order->doc_date,
                'date_out'        => $order->date_out,
                'status'          => $order->status,
                'photo_exist'     => @$order->photo_exist,
                'amount_discount' => $order->discount,
                'amount'          => 0,
            ];
            $result[] = $item;
        }

        $data = [
            'orders' => $result,
        ];

        return $data;

    }

    public function TokenPayList($key)
    {

        $json = $this->curl('TokenPayList=&SessionID=' . $key . '&json=yes', 'json');
        $data = [
            'tokens' => $json->token_pays,
        ];

        return $data;

    }

    /**
     * этот заказ может быть оплачен?
     *
     * @param integer $order_id
     * @param integer $customer_id
     *
     * @return bool
     * @throws ApiException
     */
    public function IsGoodOrder($order_id, $customer_id)
    {

        $order = urlencode(json_encode([
            'dor_id'   => $order_id,
            'contr_id' => $customer_id,
        ]));
        $action = 'IsGoodOrder=' . $order . '&json=yes';
        $this->curl($action, 'json');

        return true;

    }

    /**
     * поиск адресов по промокоду
     *
     * @param $promo
     *
     * @return array|null
     * @throws ApiException
     */
    public function PromoCode($promo)
    {

        $promo = urlencode(json_encode([
            'promo' => $promo,
        ]));
        $action = 'PromoCode=' . $promo . '&json=yes';
        $result = $this->curl($action, 'json');

        if (!empty($result->working_address) && is_array($result->working_address) && count($result->working_address) > 0) {
            return $result->working_address;
        }

        return null;

    }

    /**
     * @param string $action
     * @param string $mode xml|json
     * @param string $key
     *
     * @return mixed|\stdClass
     * @throws ApiException
     */
    private function curl($action, $mode, $key = '')
    {

        $url = Config::get('agbis.api.url') . '?' . $action;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $error = curl_error($ch);

        if ($result) {
            $this->himstatResponseLog($result, $action);
        }

        if (!$error && !$result) {
            $m = 'empty response';
            $this->himstatRequestError($m, $action);
            throw new ApiException('Отсутствует результат запроса', ApiException::ERROR_SERVER);
        }

        if ($error) {
            $this->himstatRequestError('server error: ' . $error, $action);
            throw new ApiException($error, ApiException::ERROR_SERVER);
        }


        /** @var \stdClass $xml */
        $xml = @simplexml_load_string($result);

        $xmlError = $xml ? trim(@$xml->Error->Message) : null;
        if (!empty($xmlError)) {
            $this->himstatRequestError($xmlError, $action);
            throw new ApiException($xmlError, ApiException::ERROR_DATA);
        }


        /** @var \stdClass $json */
        $json = @json_decode($result);

        $jsonError = $json ? trim(@$json->Error->Message) : null;
        if (!empty($jsonError)) {
            $jsonError = urldecode($jsonError);
            $this->himstatRequestError($jsonError, $action);
            throw new ApiException($jsonError, ApiException::ERROR_DATA);
        }
        $jsonError = $json && !empty($json->error) && $json->error > 0;
        if ($jsonError) {
            $jsonError = trim(@$json->Msg) ? trim(@$json->Msg) : 'Unknown Error';
        }
        if (!empty($jsonError)) {
            $this->himstatRequestError($jsonError, $action);
            throw new ApiException(urldecode($jsonError), ApiException::ERROR_DATA);
        }


        if ($mode == 'xml') {
            if (!$xml) {
                $this->himstatRequestError('xml parse error', $action);
                throw new ApiException('Не удалось распознать данные от сервера', ApiException::ERROR_SERVER);
            }

            if ($key) {
                return trim($xml->$key);
            }

            return $xml;
        }


        if ($mode == 'json') {
            if (!$json) {
                $this->himstatRequestError('json parse error', $action);
                throw new ApiException('Не удалось распознать данные от сервера', ApiException::ERROR_SERVER);
            }

            if ($key) {
                if (is_string($json->$key)) {
                    return trim($json->$key);
                } else {
                    return $json->$key;
                }
            }

            return $json;
        }


        return $result;

    }

    private function himstatRequestError($message, $action)
    {
        $command = explode('=', trim($action, '?& '))[0];
        $message = urldecode($message);
        Reporter::himstatRequestError($message, $action, $command);
    }

    private function himstatResponseLog($result, $action)
    {
        $command = explode('=', trim($action, '?& '))[0];
        Reporter::himstatResponseLog($result, $action, $command);
    }


    public function _cache_customer_info($key = null)
    {
        static $info = null;
        if (!$info) {
            $info = $this->ContrInfo($key);
        }

        return $info;
    }

    /**
     * найти заказ по id, если этот заказ есть в списке текущих заказов текущего клиента
     *
     * @param $order_id
     *
     * @param string $key
     *
     * @return null
     */
    public function getOrder($order_id, $key = null)
    {

        try {

            $orders = $this->Orders($key)['orders'];
            foreach ($orders as $item) {
                if ($item['id'] == $order_id) {
                    return $item;
                }
            }

        } catch (ApiException $e) {

        }

        return null;

    }

    /**
     * Прайс-лист из базы агбис
     *
     * @return mixed|\stdClass
     * @throws ApiException
     */
    public function PriceList()
    {

        $data = [
            'PriceList' => json_encode([
                'price_id' => 0
            ]),
            'json'      => 'yes',
        ];

        $url = http_build_query($data);
        $list = $this->curl($url, 'json', 'price_list');

        return $list;

    }

    /**
     * Список сертификатов
     *
     * @param null|string $key
     *
     * @return mixed|\stdClass
     * @throws ApiException
     */
    public function Certificate($key = null)
    {
        if (!$key) {
            $key = $this->key();
        }

        $url = 'Certificate=&SessionID=' . $key . '&json=yes';
        $list = $this->curl($url, 'json', 'certificate');

        foreach ($list as $item) {
            $item->name = urldecode($item->name);
            $item->comments = urldecode($item->comments);
            if (!empty($item->lines)) {
                foreach ($item->lines as $line) {
                    $line->tov_name = urldecode($line->tov_name);
                }
            }
        }

        return $list;

    }

    /**
     * Список действующих сертификатов
     *
     * @param null|string $key
     *
     * @return mixed|\stdClass
     * @throws ApiException
     */
    public function ActiveCertificates($key = null)
    {
        if (!$key) {
            $key = $this->key();
        }

        $url = 'ActiveCertificates=&SessionID=' . $key . '&json=yes';
        $list = $this->curl($url, 'json', 'active_certificates');

        foreach ($list as $item) {
            $item->cer_name = urldecode($item->cer_name);
        }

        return $list;

    }


    public function CreatePayCertificate($id, $key = null)
    {

        if (!$key) {
            $key = $this->key();
        }

        $params = urlencode('{"id": "' . $id . '"}');
        $url = 'CreatePayCertificate=' . $params . '&SessionID=' . $key . '&json=yes';

        $order_id = $this->curl($url, 'json', 'dor_id');

        return (int)$order_id;

    }


    public function FullService($id, $key = null)
    {

        if (!$key) {
            $key = $this->key();
        }

        $params = urlencode('{"dor_id": "' . $id . '"}');
        $url = 'FullService=' . $params . '&SessionID=' . $key . '&json=yes';

        $services = $this->curl($url, 'json', 'order_services');

        foreach ($services as &$item) {

            $item->service = urldecode($item->service);
            $item->status_name = urldecode($item->status_name);
            $item->nurseries_name = urldecode($item->nurseries_name);
            $item->dirty_name = urldecode($item->dirty_name);
            $item->shop_description = urldecode($item->shop_description);
            $item->ext_info = urldecode($item->ext_info);
            $item->group_tov = urldecode(@$item->group_tov);

            foreach ($item->addons as $subItem) {
                $subItem->descr = urldecode($subItem->descr);
                $subItem->aos_value = urldecode($subItem->aos_value);
            }

        }

        return $services;

    }

    public function lastDayOrders()
    {

        $customer = Customer::instance()->initByPhone('9104775209');
        $password = $customer->get()->credential->agbis_password;
        $user = $this->Login_con('+79104775209', $password);

        $params = urlencode('{"inc": "1", "part":"day"}');
        $url = 'LastOrders=' . $params . '&SessionID='. $user->key . '&json=yes';
        $orders = $this->curl($url, 'json', 'last_orders');

        return $orders;

    }


    /**
     * платеж по токену (рекарринг)
     *
     * @param $order_id
     * @param $token
     * @param $amount
     * @param $number
     *
     * @param string $key
     *
     * @return object
     */
    public function payByToken($order_id, $token, $amount, $number, $key = null)
    {

        $user = $this->ContrInfo($key);

        $url = Config::get('cloud.url');
        $post = [
            'Amount'      => $amount,
            'Currency'    => 'RUB',
            'InvoiceId'   => $order_id,
            'Description' => 'Оплата в dryharder.me заказа №' . $number,
            'AccountId'   => $user['id'],
            'Email'       => $user['email'],
            'Token'       => $token,
        ];

        Reporter::payTokenRequest($user['id'], $order_id, $url, $post);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_USERPWD, Config::get('cloud.PublicId') . ':' . Config::get('cloud.SecretKey'));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $response = @json_decode($result);
        $model = null;
        $paySuccess = false;

        Reporter::payTokenResponse($user['id'], $order_id, $result, $response, $error);

        // есть нормальный ответ
        if (!$error && !empty($response) && is_object($response)) {

            // информация о транзакции
            if (!empty($response->Model)) {
                $model = $response->Model;
            }

            // ошибка
            if ($response->Success) {
                $paySuccess = true;
            } else {
                $error = !empty($response->Message) ? $response->Message : 'Ошибка операции в платежной системе';
            }

        }

        if ($paySuccess) {
            Reporter::payTokenSuccess($user['id'], $order_id, $model);

            return (object)[
                'success' => true,
                'message' => 'Успешная оплата',
            ];
        }

        $error = trim($error . ' [code=' . $code . ']');
        Reporter::payTokenError($user['id'], $order_id, $error, $model);

        return (object)[
            'success' => false,
            'message' => $error,
        ];

    }

    public function refundPayment($paymentId, $amount)
    {
        $url = Config::get('cloud.refund');

        $post = [
            'TransactionId' => $paymentId,
            'Amount' => $amount
        ];

        Reporter::refundRequest($paymentId, $amount);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_USERPWD, Config::get('cloud.PublicId') . ':' . Config::get('cloud.SecretKey'));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $response = @json_decode($result);
        $paySuccess = false;

        Reporter::refundResponse($paymentId, $amount, $response, $error);

        // есть нормальный ответ
        if (!$error && !empty($response) && is_object($response)) {

            // ошибка
            if ($response->Success) {
                $paySuccess = true;
            } else {
                $error = !empty($response->Message) ? $response->Message : 'Ошибка операции во время возврата платежа';
            }

        }

        if ($paySuccess) {
            Reporter::refundSuccess($paymentId, $amount);

            return (object)[
                'success' => true,
                'message' => 'Возврат платежа выполнен успешно',
            ];
        }

        $error = trim($error . ' [code=' . $code . ']');
        Reporter::refundError($paymentId, $amount, $error);

        return (object)[
            'success' => false,
            'message' => $error,
        ];
    }

}