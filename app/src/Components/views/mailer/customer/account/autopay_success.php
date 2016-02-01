<?php

/**
 * @var string $type
 * @var string $pan
 * @var string $number
 * @var string $amount
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $subject ?></title>
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
                            <p>Уважаемый(ая) <?= $name ?>!</p>
                            <p>Мы произвели автоматическое списание с вашей карты
                                (<?= $type ?> <?= $pan ?>) по заказу <?= $number ?>
                                на сумму <?= $amount ?> руб.</p>
                            <p>Вы можете отключить данную функцию в вашем личном кабинете на сайте http://dryharder.me.</p>
                            <p>Если вы заметили какую либо ошибку, пожалуйста позвоните по номеру +7&nbsp;495&nbsp;666&nbsp;56&nbsp;07 или ответьте на это письмо.</p>
                        </font>
                    </td>
                </tr>
            </table>
            <table cellpadding="0" cellspacing="0" border="0" align="center">
                <tr>
                    <td width="600" valign="top">
                        <font size="3">
                            <p>С уважением,<br>команда Dry Harder</p>
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