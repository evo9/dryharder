<?php


namespace Dryharder\Components;


use Elasticsearch\Client;
use Illuminate\Validation\Validator;
use Log;

class Reporter
{


    private static $es;

    /**
     * @return Client
     */
    private static function es()
    {
        if (!self::$es) {
            self::$es = new Client();
        }

        return self::$es;
    }


    private static function report($sid, $data)
    {

        $data['ip'] = @$_SERVER['REMOTE_ADDR'];
        $data['sid'] = $sid;
        $data['dt'] = date('Y-m-d H:i:s');
        $data['date'] = date('Y-m-d');
        $data['time'] = round(microtime(true) * 1000);

        Log::debug($sid, $data);

        try {
            $prefix = \Config::get('reports.prefix');
            if($prefix){
                $prefix .= '_';
            }
            self::es()->create([
                'index' => $prefix . 'reporter',
                'type'  => $prefix . 'reporter',
                'body'  => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Exception when send Elastic Search report information (' . $e->getMessage() . ')');
        }

    }

    const LOGIN_START = 'login.start';

    public static function loginStart($phone, $password)
    {
        $message = 'начало процедуры авторизации';
        $data = compact('message', 'phone', 'password');
        self::report(self::LOGIN_START, $data);
    }

    const LOGIN_FOUND_SELF = 'login.found.self';

    public static function loginFoundSelf($customer_id)
    {
        $message = 'найден пользователь во внутренней базе';
        $data = compact('message', 'customer_id');
        self::report(self::LOGIN_FOUND_SELF, $data);
    }

    const LOGIN_PASSWORD_SELF = 'login.password.self';

    public static function loginPasswordSelf($customer_id)
    {
        $message = 'успешно авторизован по внутреннему паролю';
        $data = compact('message', 'customer_id');
        self::report(self::LOGIN_PASSWORD_SELF, $data);
    }

    const LOGIN_FAIL_VALIDATE = 'login.fail.validate';

    public static function loginFailValidate(Validator $validator)
    {
        $message = 'ошибка авторизации (валидация)';
        $data = [
            'message' => $message,
            'error'   => $validator->errors()->first(),
        ];
        self::report(self::LOGIN_FAIL_VALIDATE, $data);
    }

    const LOGIN_FAIL_EXCEPTION = 'login.fail.exception';

    public static function loginFailException(\Exception $e)
    {
        $message = 'ошибка авторизации (процесс)';
        $data = [
            'message' => $message,
            'error'   => $e->getMessage(),
        ];
        self::report(self::LOGIN_FAIL_EXCEPTION, $data);
    }

    const LOGIN_EXTERNAL = 'login.external';

    public static function loginExternal($external_id, $phone)
    {
        $message = 'успешная авторизация на внешней базе';
        $data = compact('message', 'external_id', 'phone');
        self::report(self::LOGIN_EXTERNAL, $data);
    }

    const CUSTOMER_CREATE_EXTERNAL_START = 'customer.create.external.start';

    public static function customerCreateExternalStart($external_id, $phone)
    {
        $message = 'начало создания пользователя по внешнему источнику';
        $data = compact('message', 'external_id', 'phone');
        self::report(self::CUSTOMER_CREATE_EXTERNAL_START, $data);
    }

    const CUSTOMER_CREATE_EXTERNAL_END = 'customer.create.external.end';

    public static function customerCreateExternalEnd($customer_id)
    {
        $message = 'конец создания пользователя по внешнему источнику';
        $data = compact('message', 'customer_id');
        self::report(self::CUSTOMER_CREATE_EXTERNAL_END, $data);
    }

    const CUSTOMER_UPDATE_EXTERNAL_START = 'customer.update.external.start';

    public static function customerUpdateExternalStart($customer_id, $phone, $password)
    {
        $message = 'начало обновления данных пользователя из внешнего источника';
        $data = compact('message', 'customer_id', 'phone', 'password');
        self::report(self::CUSTOMER_UPDATE_EXTERNAL_START, $data);
    }

    const CUSTOMER_UPDATE_EXTERNAL_END = 'customer.update.external.end';

    public static function customerUpdateExternalEnd($customer_id)
    {
        $message = 'конец обновления данных пользователя из внешнего источника';
        $data = compact('message', 'customer_id');
        self::report(self::CUSTOMER_UPDATE_EXTERNAL_END, $data);
    }

    const LOGIN_NEW_KEY = 'login.new.key';

    public static function loginNewKey($customer_id, $key)
    {
        $message = 'выдача нового авторизационного ключа';
        $data = compact('message', 'customer_id', 'key');
        self::report(self::LOGIN_NEW_KEY, $data);
    }

    const CUSTOMER_TOUCH_KEY = 'customer.touch.key';

    public static function customerTouchKey($key, $external_key, $customer_id)
    {
        $message = 'запрос информации по авторизационному ключу';
        $data = compact('message', 'key', 'external_key', 'customer_id');
        self::report(self::CUSTOMER_TOUCH_KEY, $data);
    }

    const CUSTOMER_EMPTY_KEY = 'customer.empty.key';

    public static function customerEmptyKey($key, $external_key)
    {
        $message = 'отсутствуют авторизационные ключи';
        $data = compact('message', 'key', 'external_key');
        self::report(self::CUSTOMER_EMPTY_KEY, $data);
    }

    const CUSTOMER_EMPTY_EXTERNAL_KEY = 'customer.empty.external.key';

    public static function customerEmptyExternalKey()
    {
        $message = 'отсутствует внешний ключ';
        $data = compact('message');
        self::report(self::CUSTOMER_EMPTY_EXTERNAL_KEY, $data);
    }

    const CUSTOMER_TOUCH_EXTERNAL_KEY = 'customer.touch.external.key';

    public static function customerTouchExternalKey($request_key, $external_key)
    {
        $message = 'запрос информации по внешнему ключу';
        $data = compact('message', 'request_key', 'external_key');
        self::report(self::CUSTOMER_TOUCH_EXTERNAL_KEY, $data);
    }

    const CUSTOMER_FAIL_EXTERNAL_KEY = 'customer.fail.external.key';

    public static function customerFailExternalKey($external_key)
    {
        $message = 'не удалось получить информацию по внешнему ключу';
        $data = compact('message', 'external_key');
        self::report(self::CUSTOMER_FAIL_EXTERNAL_KEY, $data);
    }

    const CUSTOMER_LOST_EXTERNAL_KEY = 'customer.lost.external.key';

    public static function customerLostExternalKey($external_key)
    {
        $message = 'не найден пользователь по внешнему ключу';
        $data = compact('message', 'external_key');
        self::report(self::CUSTOMER_LOST_EXTERNAL_KEY, $data);
    }

    const AGGREGATE_EXTERNAL_INFO_START = 'aggregate.external.info.start';

    public static function aggregateExternalInfoStart($external_key, $external_id, $customer_id)
    {
        $message = 'начало сбора информации из внешнего источника';
        $data = compact('message', 'external_key', 'external_id', 'customer_id');
        self::report(self::AGGREGATE_EXTERNAL_INFO_START, $data);
    }

    const AGGREGATE_EXTERNAL_INFO_END = 'aggregate.external.info.end';

    public static function aggregateExternalInfoEnd($customer_id)
    {
        $message = 'окончание сбора информации из внешнего источника';
        $data = compact('message', 'customer_id');
        self::report(self::AGGREGATE_EXTERNAL_INFO_END, $data);
    }

    const SESSION = 'session';

    public static function session()
    {
        $message = 'состояние сессии';
        $session = \Session::all();
        $data = compact('message', 'session');
        self::report(self::SESSION, $data);
    }

    const PAY_INIT = 'pay.init';

    public static function payInit($order_id, $customer_id)
    {
        $message = 'запрос данных для оплаты';
        $data = compact('message', 'customer_id', 'order_id');
        self::report(self::PAY_INIT, $data);
    }

    const PAY_INIT_ORDER_LOST = 'pay.init.order.lost';

    public static function payInitOrderLost($order_id, $customer_id)
    {
        $message = 'не найден заказ для оплаты';
        $data = compact('message', 'customer_id', 'order_id');
        self::report(self::PAY_INIT_ORDER_LOST, $data);
    }

    const PAY_INIT_ORDER_RESET = 'pay.init.order.reset';

    public static function payInitOrderReset($order_id, $customer_id)
    {
        $message = 'состояние ожидающего заказа сброшено';
        $data = compact('message', 'customer_id', 'order_id');
        self::report(self::PAY_INIT_ORDER_RESET, $data);
    }

    const PAY_INIT_ORDER_LOCKED = 'pay.init.order.locked';

    public static function payInitOrderLocked($order_id, $customer_id, $waiting, $exported)
    {
        $message = 'заказ заблокирован, уже находится в процессе оплаты';
        $data = compact('message', 'customer_id', 'order_id', 'waiting', 'exported');
        self::report(self::PAY_INIT_ORDER_LOCKED, $data);
    }

    const PAY_INIT_READY = 'pay.init.ready';

    public static function payInitReady($order_id, $customer_id, $info)
    {
        $message = 'подготовлены данные для оплаты';
        $data = compact('message', 'customer_id', 'order_id', 'info');
        self::report(self::PAY_INIT_READY, $data);
    }

    const PAY_EXTERNAL_DENY = 'pay.external.deny';

    public static function payExternalDeny($ip, $allow_ip)
    {
        $message = 'запрещен внешний запрос на оплату';
        $data = compact('message', 'ip', 'allow_ip');
        self::report(self::PAY_EXTERNAL_DENY, $data);
    }

    const PAY_EXTERNAL_ALLOW = 'pay.external.allow';

    public static function payExternalAllow($ip)
    {
        $message = 'разрешен внешний запрос на оплату';
        $data = compact('message', 'ip');
        self::report(self::PAY_EXTERNAL_ALLOW, $data);
    }

    const PAY_EXTERNAL_CHECK = 'pay.external.check';

    public static function payExternalCheck($customer_id, $order_id, $amount, $info)
    {
        $message = 'внешний запрос на проверку оплаты';
        $data = compact('message', 'customer_id', 'order_id', 'amount', 'info');
        self::report(self::PAY_EXTERNAL_CHECK, $data);
    }

    const PAY_EXTERNAL_PAY = 'pay.external.pay';

    public static function payExternalPay($customer_id, $order_id, $amount, $info)
    {
        $message = 'внешний запрос на завершение платежа';
        $data = compact('message', 'customer_id', 'order_id', 'amount', 'info');
        self::report(self::PAY_EXTERNAL_PAY, $data);
    }

    const PAY_EXTERNAL_FAIL = 'pay.external.fail';

    public static function payExternalFail($customer_id, $order_id, $amount, $info)
    {
        $message = 'внешний запрос с информацией об ошибке платежа';
        $data = compact('message', 'customer_id', 'order_id', 'amount', 'info');
        self::report(self::PAY_EXTERNAL_FAIL, $data);
    }

    const PAY_ORDER_FOUND = 'pay.order.found';

    public static function payOrderFound($customer_id, $order_id)
    {
        $message = 'заказ для оплаты найден';
        $data = compact('message', 'customer_id', 'order_id');
        self::report(self::PAY_ORDER_FOUND, $data);
    }

    const PAY_ORDER_LOST = 'pay.order.lost';

    public static function payOrderLost($customer_id, $order_id)
    {
        $message = 'не найден заказ для оплаты';
        $data = compact('message', 'customer_id', 'order_id');
        self::report(self::PAY_ORDER_LOST, $data);
    }

    const PAY_TRANSACTION_FOUND = 'pay.transaction.found';

    public static function payTransactionFound($customer_id, $order_id, $payment_id, $payment_cloud_id)
    {
        $message = 'транзакция для приема успешного платежа найдена';
        $data = compact('message', 'customer_id', 'order_id', 'payment_id', 'payment_cloud_id');
        self::report(self::PAY_TRANSACTION_FOUND, $data);
    }

    const PAY_TRANSACTION_PAID = 'pay.transaction.paid';

    public static function payTransactionPaid($customer_id, $order_id, $payment_id, $payment_cloud_id)
    {
        $message = 'транзакция отмечена как оплаченная';
        $data = compact('message', 'customer_id', 'order_id', 'payment_id', 'payment_cloud_id');
        self::report(self::PAY_TRANSACTION_PAID, $data);
    }

    const PAY_TRANSACTION_LOST = 'pay.transaction.lost';

    public static function payTransactionLost($customer_id, $order_id, $payment_id)
    {
        $message = 'не найдена транзакция для приема успешного платежа';
        $data = compact('message', 'customer_id', 'order_id', 'payment_id');
        self::report(self::PAY_TRANSACTION_LOST, $data);
    }

    const PAY_TRANSACTION_FAIL = 'pay.transaction.fail';

    public static function payTransactionFail($customer_id, $order_id, $payment_id)
    {
        $message = 'не удалось создать транзакцию для приема успешного платежа';
        $data = compact('message', 'customer_id', 'order_id', 'payment_id');
        self::report(self::PAY_TRANSACTION_FAIL, $data);
    }

    const PAY_TRANSACTION_CREATED = 'pay.transaction.created';

    public static function payTransactionCreated($customer_id, $order_id, $payment_id, $payment_cloud_id)
    {
        $message = 'создана транзакция для приема успешного платежа';
        $data = compact('message', 'customer_id', 'order_id', 'payment_id', 'payment_cloud_id');
        self::report(self::PAY_TRANSACTION_CREATED, $data);
    }

    const PAY_RESPONSE_ERROR = 'pay.response.error';

    public static function payResponseError($customer_id, $order_id, $payment_id, $error_code)
    {
        $message = 'ответ платежному шлюзу - ошибка';
        $data = compact('message', 'customer_id', 'order_id', 'payment_id', 'error_code');
        self::report(self::PAY_RESPONSE_ERROR, $data);
    }

    const PAY_RESPONSE_SUCCESS = 'pay.response.success';

    public static function payResponseSuccess($customer_id, $order_id, $payment_id)
    {
        $message = 'успешный ответ платежному шлюзу';
        $data = compact('message', 'customer_id', 'order_id', 'payment_id');
        self::report(self::PAY_RESPONSE_SUCCESS, $data);
    }

    const PAY_EXTERNAL_PARAMETERS_FAIL = 'pay.external.parameters.fail';

    public static function payExternalParametersFail($parameters)
    {
        $message = 'ошибка в параметрах внешнего запроса';
        $data = compact('message', 'parameters');
        self::report(self::PAY_EXTERNAL_PARAMETERS_FAIL, $data);
    }

    const PAY_EXTERNAL_PARAMETERS = 'pay.external.parameters';

    public static function payExternalParameters($parameters)
    {
        $message = 'подготовленные праметры запроса на оплату';
        $data = compact('message', 'parameters');
        self::report(self::PAY_EXTERNAL_PARAMETERS, $data);
    }

    const PAY_TOKEN_REQUEST = 'pay.token.request';

    public static function payTokenRequest($customer_id, $order_id, $url, $post)
    {
        $post['url'] = $url;
        $message = 'начало оплаты по токену';
        $data = compact('message', 'customer_id', 'order_id', 'post');
        self::report(self::PAY_TOKEN_REQUEST, $data);
    }

    const PAY_TOKEN_RESPONSE = 'pay.token.request';

    public static function payTokenResponse($customer_id, $order_id, $result, $response, $error)
    {
        $message = 'ответ платежной системы на оплату по токену';
        $data = compact('message', 'order_id', 'customer_id', 'result', 'response', 'error');
        self::report(self::PAY_TOKEN_RESPONSE, $data);
    }

    const PAY_TOKEN_SUCCESS = 'pay.token.success';

    public static function payTokenSuccess($customer_id, $order_id, $info)
    {
        $message = 'успешная оплата по токену';
        $data = compact('message', 'customer_id', 'order_id', 'info');
        self::report(self::PAY_TOKEN_SUCCESS, $data);
    }

    const PAY_TOKEN_ERROR = 'pay.token.error';

    public static function payTokenError($customer_id, $order_id, $error, $info)
    {
        $message = 'ошибка оплаты по токену';
        $data = compact('message', 'customer_id', 'order_id', 'error', 'info');
        self::report(self::PAY_TOKEN_ERROR, $data);
    }

    const ERROR_LOST_EXTERNAL_CUSTOMER = 'error.lost.external.customer';

    public static function errorLostExternalCustomer($customer_id, $external_user)
    {
        $message = 'не найден пользователь в локальной базе по внешнему ключу';
        $data = compact('message', 'customer_id', 'external_user');
        self::report(self::ERROR_LOST_EXTERNAL_CUSTOMER, $data);
    }

    const INVITE_CODE_FOUND = 'invite.code.found';

    public static function inviteCodeFound($customer_id, $owner_id)
    {
        $message = 'найден владелец инвайта по регистрационному номеру телефона';
        $data = compact('message', 'customer_id', 'owner_id');
        self::report(self::INVITE_CODE_FOUND, $data);
    }

    const INVITE_CODE_REGISTERED = 'invite.code.registered';

    public static function inviteCodeRegistered($customer_id, $owner_id, $customer_invite_id)
    {
        $message = 'регистрация по инвайту';
        $data = compact('message', 'customer_id', 'owner_id', 'customer_invite_id');
        self::report(self::INVITE_CODE_REGISTERED, $data);
    }

    const INVITE_CODE_FOUND_EXTERNAL = 'invite.code.found.external';

    public static function inviteCodeFoundExternal($phone, $owner_id, $code)
    {
        $message = 'найден владелец инвайта по коду при регистрации во внешней системе';
        $data = compact('message', 'phone', 'owner_id', 'code');
        self::report(self::INVITE_CODE_FOUND_EXTERNAL, $data);
    }

    const INVITE_CODE_REGISTERED_EXTERNAL = 'invite.code.registered.external';

    public static function inviteCodeRegisteredExternal($phone, $owner_id, $customer_invite_id, $code)
    {
        $message = 'регистрация по инвайту и внешнему id клиента';
        $data = compact('message', 'phone', 'owner_id', 'customer_invite_id', 'code');
        self::report(self::INVITE_CODE_REGISTERED_EXTERNAL, $data);
    }

    const HIMSTAT_REQUEST_ERROR = 'himstat.request.error';

    public static function himstatRequestError($error, $action, $command)
    {
        $message = '[' . $command . '] ошибка выполнения запроса к api агбис';
        $data = compact('message', 'error', 'action', 'command');
        self::report(self::HIMSTAT_REQUEST_ERROR, $data);
    }

    const HIMSTAT_RESPONSE_LOG = 'himstat.response.log';

    public static function himstatResponseLog($result, $action, $command)
    {
        if (is_object($result)) {
            foreach ($result as $key => $value) {
                $result->$key = urldecode($value);
            }
        }
        if (is_string($result)) {
            $result = urldecode($result);
        }
        $message = '[' . $command . '] ответ на запрос в агбис';
        $data = compact('message', 'result', 'action', 'command');
        self::report(self::HIMSTAT_RESPONSE_LOG, $data);
    }

    const SUBSCRIPTION_PAYMENT_REQUEST = 'subscription.payment.request';
    public static function subscriptionPaymentRequest($customer_id, $subscription_id)
    {
        $message = 'новый запрос на оплату подписки';
        $data = compact('message', 'customer_id', 'subscription_id');
        self::report(self::SUBSCRIPTION_PAYMENT_REQUEST, $data);
    }

    const SUBSCRIPTION_FOUND = 'subscription.found';
    public static function subscriptionFound($customer_id, $subscription_id, $info)
    {
        $message = 'запрашиваемая подписка найдена';
        $data = compact('message', 'customer_id', 'subscription_id', 'info');
        self::report(self::SUBSCRIPTION_FOUND, $data);
    }

    const SUBSCRIPTION_NOT_FOUND = 'subscription.not.found';
    public static function subscriptionNotFound($customer_id, $subscription_id)
    {
        $message = 'не найдена запрашиваемая подписка';
        $data = compact('message', 'customer_id', 'subscription_id');
        self::report(self::SUBSCRIPTION_NOT_FOUND, $data);
    }

    const SUBSCRIPTION_CREATED = 'subscription.created';
    public static function subscriptionCreated($customer_id, $subscription_id)
    {
        $message = 'подписка создана во внутренней базе';
        $data = compact('message', 'customer_id', 'subscription_id');
        self::report(self::SUBSCRIPTION_CREATED, $data);
    }

    const SUBSCRIPTION_ORDER_CREATED = 'subscription.order.created';
    public static function subscriptionOrderCreated($customer_id, $subscription_id, $order_id)
    {
        $message = 'создан заказ в агбисе по запросу подписки';
        $data = compact('message', 'customer_id', 'subscription_id', 'order_id');
        self::report(self::SUBSCRIPTION_ORDER_CREATED, $data);
    }



}