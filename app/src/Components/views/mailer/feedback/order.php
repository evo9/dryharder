<?php

/**
 * @var string $subject
 * @var string $name
 * @var string $email
 * @var string $text
 * @var string $phone
 * @var string $stars
 * @var string $order
 * @var string $doc_number
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $subject ?></title>
    <style type="text/css">
        <?php include(__DIR__ . '/../boilerplate/head.css') ?>
    </style>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
    <tr>
        <td>

            <table cellpadding="0" cellspacing="0" border="0" align="center">

                <tr>
                    <td width="600" valign="top">
                        <font size="3"> Получен новый отзыв с сайта Dry Harder от <?= e($name) ?>.<br><br>
                            Контакты: <span class="mobile_link"><?= e($email) ?></span>,
                            <span class="mobile_link"><?= e($phone) ?></span>
                            <br><br>
                            <?php if($stars > 0){?>
                                Клиент оценил <?= $order > 0 ? 'заказ': 'заявку' ?> на: <?= e($stars) ?>
                            <?php }else{ ?>
                                Клиент не поставил оценку
                            <?php } ?>
                        </font>
                    </td>
                </tr>

                <?php if($text){ ?>
                    <tr>
                        <td width="600" valign="top">
                            <font size="3">
                                Текст сообщения:
                                <br><br>
                                <?= str_replace("\n", '<br><br>', e($text)) ?>
                            </font>
                        </td>
                    </tr>
                <?php } ?>

                <tr>
                    <td width="600" valign="top">
                        <font size="3">
                            <br><br>
                            Отзыв оставлен к <?= $order > 0 ? 'заказу': 'заявке' ?> номер: <?= e($doc_number) ?> (внутренний id: <?= e($order) ?>)
                        </font>
                    </td>
                </tr>

            </table>

            <?php include(__DIR__ . '/../boilerplate/header.php') ?>

        </td>
    </tr>
</table>
</body>
</html>