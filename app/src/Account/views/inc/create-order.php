<?php
/**
 * @var boolean $account
 */

require (__DIR__ . '/order/days.php');
$class = $account ? 'registration-form' : 'location-form';
require(__DIR__ . '/order/styles.php');
?>

<form action="/" method="POST" class="<?= $class ?>" id="new-order-form" data-type="account" onsubmit="return false;">

    <fieldset>
        <?php require(__DIR__ . '/order/addresses.php')  ?>
    </fieldset>

    <div class="button-holder">
        <button type="submit" class="btn btn-primary col-lg-12 pull-right" data-loading-text="<?= trans('request.sendOrder') ?>...">
            <?= trans('request.getOrder') ?>! <i class="fa fa-shopping-cart"></i></button>
        <div class="clear clearfix"></div>
    </div>
</form>