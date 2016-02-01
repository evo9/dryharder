<?php

use Dryharder\Components\Order;


foreach ($orders as $item) {

    $num = isset($num) ? $num + 1 : 1;
    $first = ($num == 1) ? ' first' : '';

    $classExists = '';
    if(!empty($item['review_id'])){
        $classExists = ' review-exists';
    }

    ?>
    <li>

        <a href="#" class="opener"> <span class="cell"><?= $num ?></span>
            <span class="cell"><?= $item['date_in'] ?></span>
            <span class="cell"><?= Order::statusName($item['status']) ?></span>
            <span class="cell"><?= $item['amount_credit'] ?> <i class="fa fa-rub"></i></span> </a>

        <div class="slide">
            <div class="frame">
                <div class="small-table">
                    <div class="row-holder">
                        <div class="column">
                            <strong class="title"><?= trans('main.Review') ?></strong>
                            <p><a href="#" onclick="return false;" class="link-icons open-review-modal <?= $classExists ?>" data-review="<?= @$item['review_id'] ?>" data-order="<?= $item['id'] ?>" data-sum="<?= (int)$item['amount'] ?>.00"><i class="fa fa-pencil"></i></a></p>
                        </div>
                    </div>
                    <div class="row-holder">
                        <div class="column">
                            <strong class="title"><?= trans('main.Order no.') ?></strong>

                            <p><?= $item['doc_number'] ?></p>
                        </div>
                        <div class="column">
                            <strong class="title"><?= trans('main.Issue date') ?></strong>

                            <p><?= $item['date_out'] ?></p>
                        </div>
                    </div>
                    <div class="row-holder">
                        <div class="column">
                            <strong class="title"><?= trans('main.Due') ?></strong>

                            <p><?= $item['amount_credit'] ?> <i class="fa fa-rub"></i></p>
                        </div>
                        <div class="column">
                            <strong class="title"><?= trans('main.Paid') ?></strong>

                            <p><?= $item['amount_debit'] ?> <i class="fa fa-rub"></i></p>
                        </div>
                    </div>
                </div>
                <a href="#" class="btn btn-info btn-xs on-order-details-modal paybtn<?= $item['id'] ?>" data-order_id="<?= $item['id'] ?>" data-paid="<?= (int)($item['amount'] <= 0) ?>" data-sum="<?= (int)$item['amount'] ?>.00"><?= trans('main.View order') ?>
                    <i class="fa fa-th-list"></i></a>
                <a href="#" class="btn btn-primary button-holder checkout-accordion" data-paybtn="paybtn<?= $item['id'] ?>" <?= ($item['amount'] <= 0) ? 'style="display: none"' : '' ?> ><?= trans('main.Checkout') ?>
                    <i class="fa fa-shopping-cart"></i></a>
            </div>
        </div>

    </li>

<?php } ?>