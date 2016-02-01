<?php
use Dryharder\Components\Order;
use Dryharder\Models\OrderRequest;

/**
 * @var array $orders
 * @var OrderRequest[] $requests
 */

?>

    <div class="heading">
        <div class="column"><?= trans('main.No.') ?></div>
        <div class="column"><?= trans('main.Order no.') ?></div>
        <div class="column"><?= trans('main.Order date') ?></div>
        <div class="column"><?= trans('main.Issue date') ?></div>
        <div class="column"><?= trans('main.Status') ?></div>
        <div class="column"><?= trans('main.Due') ?></div>
        <div class="column"><?= trans('main.Paid') ?></div>
        <div class="column"><?= trans('main.Review') ?></div>
    </div>

<?php

if(!isset($requests) || empty($requests)){
    $requests = [];
}
foreach($requests as $request){

    ?>
    <div class="row-holder">
        <div class="column"></div>
        <div class="column">
            <p><?= $request->getHumanId() ?></p>
        </div>
        <div class="column">
            <p><?= date('d/m/Y', strtotime($request->created_at)) ?></p>
        </div>
        <div class="column">
            <p></p>
        </div>
        <div class="column">
            <p>заявка получена</p>
        </div>
        <div class="column">
            <p></p>
        </div>
        <div class="column">
            <p></p>
        </div>
        <div class="column column-review">
            <p><a href="#" class="icon-link open-review-modal" data-request="1" data-review="<?= @$request->reviewId() ?>" data-order="<?= $request->id ?>" onclick="return false;"><i class="fa fa-pencil"></i></a></p>
        </div>
    </div>

    <?php
}

foreach ($orders as $item) {

    $num = isset($num) ? $num + 1 : 1;
    $first = ($num == 1) ? ' first' : '';

    $classExists = '';
    if(!empty($item['review_id'])){
        $classExists = ' review-exists';
    }


    ?>
    <div class="row-holder<?= $first ?>">
        <div class="column">.<?= $num ?></div>
        <div class="column">
            <p><?= $item['doc_number'] ?></p>
        </div>
        <div class="column">
            <p><?= $item['date_in'] ?></p>
        </div>
        <div class="column">
            <p><?= $item['date_out'] ?></p>
        </div>
        <div class="column">
            <p><?= Order::statusName($item['status']) ?></p>
        </div>
        <div class="column">
            <p><?= $item['amount_credit'] ?><i class="fa fa-rub"></i></p>
        </div>
        <div class="column">
            <p><?= $item['amount_debit'] ?><i class="fa fa-rub"></i></p>
        </div>
        <a href="#" data-order_id="<?= $item['id'] ?>" data-paid="<?= (int)($item['amount'] <= 0) ?>" data-sum="<?= (int)$item['amount'] ?>.00" class="on-order-details tab-link active"></a>
        <div class="column column-review">
            <p><a href="#" class="icon-link open-review-modal <?= $classExists ?>" data-review="<?= @$item['review_id'] ?>" data-order="<?= $item['id'] ?>" data-sum="<?= (int)$item['amount'] ?>.00" onclick="return false;"><i class="fa fa-pencil"></i></a></p>
        </div>
    </div>

<?php } ?>