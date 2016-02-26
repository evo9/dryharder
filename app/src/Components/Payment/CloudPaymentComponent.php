<?php


namespace Dryharder\Components\Payment;


class CloudPaymentComponent
{
    public static function getCloudError($code)
    {
        $errors = [
            '5001' => [
                'title' => 'Refer To Card Issuer',
                'reason' => 'Отказ эмитента проводить онлайн операцию',
                'description' => 'Свяжитесь с вашим банком или воспользуйтесь другой картой'
            ],
            '5005' => [
                'title' => 'Do Not Honor',
                'reason' => 'Отказ эмитента без объяснения причин',
                'description' => 'Свяжитесь с вашим банком или воспользуйтесь другой картой'
            ],
            '5006' => [
                'title' => 'Error',
                'reason' => 'Отказ сети проводить операцию или неправильный CVV код',
                'description' => 'Проверьте реквизиты или воспользуйтесь другой картой'
            ],
            '5012' => [
                'title' => 'Invalid Transaction',
                'reason' => 'Карта не предназначена для онлайн платежей',
                'description' => 'Воспользуйтесь другой картой'
            ],
            '5013' => [
                'title' => 'Amount Error',
                'reason' => 'Слишком маленькая или слишком большая сумма операции',
                'description' => 'Проверьте сумму'
            ],
            '5030' => [
                'title' => 'Format Error',
                'reason' => 'Ошибка на стороне эквайера — неверно сформирована транзакция',
                'description' => 'Повторите попытку позже'
            ],
            '5031' => [
                'title' => 'Bank Not Supported By Switch',
                'reason' => 'Неизвестный эмитент карты',
                'description' => 'Воспользуйтесь другой картой'],
            '5034' => [
                'title' => 'Suspected Fraud',
                'reason' => 'Отказ эмитента — подозрение на мошенничество',
                'description' => 'Свяжитесь с вашим банком или воспользуйтесь другой картой'
            ],
            '5041' => [
                'title' => 'Lost Card',
                'reason' => 'Карта потеряна',
                'description' => 'Свяжитесь с вашим банком'
            ],
            '5043' => [
                'title' => 'Stolen Card',
                'reason' => 'Карта украдена',
                'description' => 'Свяжитесь с вашим банком'
            ],
            '5051' => [
                'title' => 'Insufficient Funds',
                'reason' => 'Недостаточно средств',
                'description' => 'Недостаточно средств на карте'
            ],
            '5054' => [
                'title' => 'Expired Card',
                'reason' => 'Карта просрочена или неверно указан срок действия',
                'description' => 'Проверьте реквизиты или воспользуйтесь другой картой'
            ],
            '5057' => [
                'title' => 'Transaction Not Permitted',
                'reason' => 'Ограничение на карте',
                'description' => 'Свяжитесь с вашим банком или воспользуйтесь другой картой'
            ],
            '5065' => [
                'title' => 'Exceed Withdrawal Frequency',
                'reason' => 'Превышен лимит операций по карте',
                'description' => 'Свяжитесь с вашим банком или воспользуйтесь другой картой'
            ],
            '5082' => [
                'title' => 'Incorrect CVV',
                'reason' => 'Неверный CVV код',
                'description' => 'Проверьте реквизиты или воспользуйтесь другой картой'
            ],
            '5091' => [
                'title' => 'Timeout',
                'reason' => 'Эмитент недоступен',
                'description' => 'Повторите попытку позже или воспользуйтесь другой картой'
            ], '5092' => [
                'title' => 'Cannot Reach Network',
                'reason' => 'Эмитент недоступен',
                'description' => 'Повторите попытку позже или воспользуйтесь другой картой'
            ],
            '5096' => [
                'title' => 'System Error',
                'reason' => 'Ошибка банка-эквайера или сети',
                'description' => 'Повторите попытку позже'
            ],
            '5204' => [
                'title' => 'Unable To Process',
                'reason' => 'Операция не может быть обработана по прочим причинам',
                'description' => 'Повторите попытку позже или воспользуйтесь другой картой'
            ],
            '5206' => [
                'title' => 'Authentication failed',
                'reason' => '3-D Secure авторизация не пройдена',
                'description' => 'Свяжитесь с вашим банком или воспользуйтесь другой картой'
            ],
            '5207' => [
                'title' => 'Authentication unavailable',
                'reason' => '3-D Secure авторизация недоступна',
                'description' => 'Свяжитесь с вашим банком или воспользуйтесь другой картой'
            ],
            '5300' => [
                'title' => 'Anti Fraud',
                'reason' => 'Лимиты эквайера на проведение операций',
                'description' => 'Воспользуйтесь другой картой'
            ]
        ];

        return isset($errors[$code]) ? $errors[$code] : null;
    }
}