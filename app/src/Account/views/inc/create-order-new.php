<?php
/**
 * @var boolean $account
 */

require (__DIR__ . '/order/days.php');
$class = $account ? 'registration-form' : 'location-form';
require(__DIR__ . '/order/styles.php');
?>

<form action="/" method="POST" class="<?= $class ?>" id="new-order-form" data-type="new" onsubmit="return false;">

    <fieldset>

        <div class="column-form column-form-left">
            <h4><?= trans('request.mobilePhone') ?>:</h4>
            <div class="row-holder pb20">
                <div class="input-holder input-group">
                    <span class="input-group-addon plus-seven">+7</span>
                    <input type="text" id="f101" placeholder="" name="phone" value="">
                </div>
            </div>
        </div>

        <div class="column-form column-form-right">
            <h4><?= trans('request.email') ?>:</h4>
            <div class="row-holder pb20">
                <div class="input-holder">
                    <input type="text" id="f101" placeholder="" name="email" value="">
                </div>
            </div>
        </div>

        <div class="clear clearfix"></div>

        <?php require(__DIR__ . '/order/addresses.php')  ?>

    </fieldset>

    <div class="button-holder">
        <button type="submit" class="btn btn-primary col-lg-12 pull-right" data-loading-text="<?= trans('request.sendOrder') ?>...">
            <?= trans('request.getOrder') ?>! <i class="fa fa-shopping-cart"></i></button>
        <div class="clear clearfix"></div>
    </div>
</form>