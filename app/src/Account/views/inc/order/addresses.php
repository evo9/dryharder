<?php

/**
 * @var array $user
 */

$user = !empty($user)
    ? $user
    : ['address' => ''];

?>

<!--suppress HtmlFormInputWithoutLabel -->
<div class="column-form column-form-left">
    <h4><?= trans('request.addressTo') ?>:</h4>

    <div class="row-holder pb20">
        <div class="input-holder">
            <input type="text" id="f101" placeholder="<?= trans('request.addressPlaceholder') ?>" name="address1" value="<?= e($user['address']) ?>">
        </div>
    </div>
</div>

<div class="column-form column-form-right">

    <h4><?= trans('request.addressFrom') ?>:</h4>

    <div class="row-holder pb20">
        <div class="input-holder">
            <input type="text" id="f103" placeholder="<?= trans('request.addressPlaceholder') ?>" name="address2" value="<?= e($user['address']) ?>">
        </div>
    </div>

</div>
<div class="clear clearfix"></div>

<?php if($fromOrderText && $toOrderText){ ?>
    <div class="column-form column-form-left">
        <button class="btn btn-primary select-mode order-standard selected" type="button"><i class="fa fa-check-circle"></i> <?= trans('request.standardOrder') ?><br>
            <small><?= trans('request.selectPickUpAndDelivery') ?></small>
        </button>
    </div>
    <div class="column-form column-form-right">
        <button class="btn btn-default select-mode order-fast" type="button"><i class="fa fa-circle-o"></i> <?= trans('request.fastOrderTip') ?><br>
            <small><?= $fromOrderText ?><br><?= $toOrderText ?></small>
        </button>
    </div>
    <div class="clear clearfix pb20"></div>
<?php } ?>


<div class="column-form column-form-left order-standard-only">
    <div class="row-holder">
        <div class="label-holder">
            <label><?= trans('request.selectStartDateTime') ?>:</label>
        </div>
        <div>
            <ul class="select-day word day left">
                <?php
                $first = 1;
                $delimiter = true;
                $prev = '';
                foreach($fromNamesShort as $key => $val){
                    ?><li><a href="#" onclick="return false;" data-disabled="<?= implode('|', $disabled[$key]) ?>" data-key="<?= $key ?>" data-first="<?= $first ?>" data-prev="<?= $prev ?>" class="on-day-left"><?= $val ?></a></li><?php
                    $first = 0;
                    $prev = $key;
                    if($key > 1 && $delimiter){
                        $delimiter = false;
                        echo '</ul><ul class="select-day word day left">';
                    }
                }
                ?>
            </ul>
            <div class="clear clearfix"></div>
            <ul class="select-day time left">
                <li class="postfix"><?= trans('request.hours') ?></li>
                <li data-label="7:00 - 12:00"><a href="#" onclick="return false;" data-key="7" class="on-time-left">7-12</a></li>
                <li data-label="12:00 - 17:00"><a href="#" onclick="return false;" data-key="12" class="on-time-left">12-17</a></li>
                <li data-label="17:00 - 23:00"><a href="#" onclick="return false;" data-key="17" class="on-time-left">17-23</a></li>
            </ul>
            <div class="clear clearfix"></div>
            <p class="small" style="color: grey; margin-left: 20px; margin-top: 10px;">* <?= trans('request.selectStartDateTimePs') ?></p>
        </div>
    </div>

</div>

<div class="column-form column-form-right order-standard-only">
    <div class="row-holder">
        <div class="label-holder">
            <label><?= trans('request.selectDeliveryDateTime') ?>:</label>
        </div>
        <div>
            <ul class="select-day word day right">
                <?php
                $first = 1;
                $delimiter = true;
                $prev = array_keys($fromNamesShort)[0];
                foreach($fromNamesShort2 as $key => $val){
                    ?><li><a href="#" onclick="return false;" data-key="<?= $key ?>" data-first="<?= $first ?>" data-prev="<?= $prev ?>" class="on-day-right"><?= $val ?></a></li><?php
                    $first = 0;
                    $prev = $key;
                    if($key > 1 && $delimiter){
                        $delimiter = false;
                        echo '</ul><ul class="select-day word day right">';
                    }
                }
                ?>
            </ul>
            <div class="clear clearfix"></div>
            <ul class="select-day time right">
                <li class="postfix"><?= trans('request.hours') ?></li>
                <li data-label="7:00 - 12:00"><a href="#" onclick="return false;" data-key="7" class="on-time-right">7-12</a></li>
                <li data-label="12:00 - 17:00"><a href="#" onclick="return false;" data-key="12" class="on-time-right">12-17</a></li>
                <li data-label="17:00 - 23:00"><a href="#" onclick="return false;" data-key="17" class="on-time-right">17-23</a></li>
            </ul>
            <div class="clear clearfix"></div>
        </div>
    </div>

</div>


<div class="clear clearfix"></div>
<div class="col-lg-12 checkbox">
    <input type="checkbox" value="1" name="time_strict" id="time-strict" class="label-colored strict-checkbox">
    <label for="time-strict"><?= trans('request.plusFastDeliveryTime') ?></label>
</div>
<div class="clear clearfix"></div>
<div class="col-lg-12 checkbox">
    <input type="checkbox" id="notWantDescription" class="label-colored logic-checkbox" checked>
    <label for="notWantDescription" class="checked"><?= trans('request.notWantDescription') ?></label>
</div>

<div class="clear clearfix"></div>
<div class="order-terms" style="padding: 5px 10px 10px 5px; margin: 5px 10px 10px 5px; border: 1px solid grey;">
    <h4>Внимание: условия заказа</h4>
    <ul>
        <li class="nwdText" style="display: none;"><?= trans('request.pleaseDescription') ?>.</li>
        <li class="fastText" style="display: none;"><?= $fromOrderText ?></li>
        <li class="fastText" style="display: none;"><?= $toOrderText ?></li>
        <li class="fromText" style="display: none;"></li>
        <li class="toText" style="display: none;"></li>
        <li><?= trans('request.attention') ?>.</li>
        <li class="fastText" style="display: none;"><?= trans('request.fastOrderText') ?>.</li>
        <li class="strictText" style="display: none;"><?= trans('request.strictOrderText') ?>.</li>
    </ul>
</div>

<div class="clear clearfix"></div>
<div class="col-lg-3 comment-label">
    <h4><?= trans('request.comment') ?>:</h4>
    <small class="small"><?= trans('request.orderComment') ?></small>
</div>
<div class="col-lg-9">
    <div class="input-holder">
        <textarea name="comment" class="textarea-comment"></textarea>
    </div>
</div>

