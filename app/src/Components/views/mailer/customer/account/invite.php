<?php

/**
 * @var Customer $customer
 * @var Customer $owner
 */
use Dryharder\Models\Customer;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Бонус за покупку Друга</title>
    <style type="text/css">
        <?php include(__DIR__ . '/../../boilerplate/head.css') ?>
    </style>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
    <tr>
        <td>

            <table cellpadding="0" cellspacing="0" border="0" align="center">
                <tr>
                    <td width="600" valign="top">
                        <font size="3">
                            <p>Приглашенный Друг оплатил заказ.</p>
                            <p>Нужно выполнить обещание и начислить владельцу ссылки - бонусы за Друга!</p>
                            <p>Данные владельца ссылки: <?= e($owner->name) . ' ['. e($owner->phone) .']' ?></p>
                            <p>Данные приглашенного Друга: <?= e($customer->name) . ' ['. e($customer->phone) .']' ?></p>
                        </font>
                    </td>
                </tr>
            </table>

            <?php include(__DIR__ . '/../../boilerplate/header.php') ?>

        </td>
    </tr>
</table>
</body>
</html>