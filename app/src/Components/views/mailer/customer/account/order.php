<?php

/**
 * @var StdClass $order
 * @var array $services
 * @var string $subject
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
        body, #header h1, #header h2, p {margin: 0; padding: 0;}
        #services td { padding: 4px; }
        #services tr { border-bottom: 1px solid grey; }
        #servicesTitle td { background-color: #2dd982; color: #ffffff; }
    </style>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
    <tr>
        <td>

            <table cellpadding="0" cellspacing="0" border="0" align="center" width="600">
                <tr>
                    <td width="600" valign="top">
                        <font size="3">
                            <p>Состав заказа <?= $order['doc_number'] ?>.</p>
                            <p>Клиент: <?= $name ?>.</p>
                            <br>
                        </font>
                    </td>
                </tr>
                <tr>
                    <td width="600" valign="top">
                        <table id="services" cellpadding="4" cellspacing="0" border="0" align="center" width="600">
                            <tr id="servicesTitle" bgcolor="2dd982" style="background-color: #2dd982;">
                                <td cellpadding="4" style="background-color: #2dd982; color: #ffffff;"><font size="2" align="left" color="ffffff"><strong>Название</strong></font></td>
                                <td cellpadding="4" style="background-color: #2dd982; color: #ffffff;"><font size="2" align="right" color="ffffff"><strong>Сумма</strong></font></td>
                                <td cellpadding="4" style="background-color: #2dd982; color: #ffffff;"><font size="2" align="center" color="ffffff"><strong>Количество</strong></font></td>
                            </tr>
                            <?php
                            $odd = 'asd';
                            foreach($services as $item){
                                $odd = !$odd ? ' bgcolor="F2F2F2" style="background-color: #F2F2F2;"': '';
                                ?>
                                <tr<?=$odd?>>
                                    <td cellpadding="4"><font size="2" align="left"><?= $item['name'] ?></font></td>
                                    <td cellpadding="4"><font size="2" align="right"><?= $item['amount'] ?> руб.</font></td>
                                    <td cellpadding="4"><font size="2" align="center"><?= $item['qnt'] ?></font></td>
                                </tr>
                            <?php } ?>
                        </table>

                        <font size="3">
                            <br>
                            <p>Сумма: <?= $order['amount_credit'] ?> руб.</p>
                            <p>К оплате: <?= $order['amount'] ?> руб.</p>
                            <br>
                            <p>
                                Большое спасибо, что являетесь нашим клиентом!<br>
                                Если у вас есть вопросы или отзывы, пожалуйста, свяжитесь с нами:<br>
                                <a href="http://dryharder.me/" target="_blank" title="Dry Harder" style="color: green; text-decoration: none;" color="green">http://dryharder.me/</a>,
                                <a style="color: green; text-decoration: none;" href="tel:+74956665607" color="green">+7 495 666-56-07</a><br>
                                или ответьте на это письмо.
                            </p>
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