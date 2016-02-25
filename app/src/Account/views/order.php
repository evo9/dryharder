<?php if ($showButton) : ?>
    <div class="button-holder checkout" data-id="<?php echo $id; ?>" data-sum="<?php echo $total; ?>">
        <a href="javascript:;" class="btn btn-primary"><?= trans('main.Checkout') ?> <i class="fa fa-shopping-cart"></i></a>
    </div>
<?php endif; ?>
<div class="orders-table current-orders">
    <?php
    /**
     * @var integer $id
     */
    ?>

    <div class="heading">
        <div class="column">
            <strong><?= trans('main.No.') ?></strong>
        </div>
        <div class="column">
            <strong><?= trans('main.Items') ?></strong>
            <a href="/account/order/services/pdf/<?= $id ?>" target="_blank" class="download-clothes-description">
                <i class="fa fa-download" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?= trans('main.clothes description all') ?>"></i>
            </a>
        </div>
    </div>

    <?php

    /**
     * @var array[] $services
     */

    foreach ($services as $item) {
        $num = isset($num) ? $num + 1 : 1;
        $first = ($num == 1) ? ' first' : '';
        $qnt = ($item['qnt'] > 1) ? ' (x' . $item['qnt'] . ')' : '';

        ?>

        <div class="row-holder<?= $first ?>">
            <div class="column number">
                <p><?= $num ?></p>
            </div>
            <div class="column title">
                <p>
                    <?= truncate($item['name'], 30) ?>
                    <?php if(!empty($item['properties'])){ ?>
                        <a href="#" class="detail-order-hover">
                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?= trans('main.clothes description') ?>"></i>
                        </a>
                    <?php } ?>
                    <br>
                    <?= $item['amount'] ?> <?= $qnt ?> <i class="fa fa-rub"></i>
                </p>
                <?php if(!empty($item['properties'])){ ?>
                    <div class="detail-order-content" style="display: none;">
                        <h4><?= $item['name'] ?></h4>
                        <h5><?= $item['amount'] ?> <?= $qnt ?> <i class="fa fa-rub"></i></h5>
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
                    </div>
                <?php } ?>
            </div>
        </div>

    <?php } ?>
</div>

