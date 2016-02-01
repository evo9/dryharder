<?php

/**
 * @var array $services
 * @var array $order
 */

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

    <img src="http://dryharder.me/images/fixed-logo.png" width="78" height="78" style="width: 78px; height: 78px; float: right;">
    <p style="text-align: left;">
        Состав заказа <?= $order['doc_number'] ?>
        <br>
        Клиент: <?= $name ?>
    </p>

    <table>
        <?php foreach($services as $item){ ?>
            <tr>
                <td class="title"><?= $item['name'] ?></td>
                <td class="title"><?= $item['price'] ?> руб.</td>
                <td class="title"><?= $item['qnt'] ?></td>
            </tr>
            <?php if(!empty($item['properties'])){ ?>
                <tr>
                    <td colspan="3">
                        <ul>
                            <?php foreach($item['properties'] as $property){ ?>
                                <li>
                                    <?php
                                    echo $property['title'];
                                    if(!empty($property['value'])){
                                        echo ': ' . $property['value'];
                                    }
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>

    <p>Сумма: <?= $order['amount_credit'] ?> руб.</p>
    <p>К оплате: <?= $order['amount'] ?> руб.</p>

    <p>
        Большое спасибо, что являетесь нашим клиентом!<br>
        Если у вас есть вопросы или отзывы, пожалуйста, свяжитесь с нами:<br>
        <a href="http://dryharder.me/" target="_blank" title="Dry Harder" style="color: #2edf86; text-decoration: none;" color="2edf86">http://dryharder.me/</a>,
        <a style="color: #2edf86; text-decoration: none;" href="tel:+74956665607" color="2edf86">+7 495 666-56-07</a>.
    </p>

</body>
</html>
