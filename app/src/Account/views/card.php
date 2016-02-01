<?php

/**
 * @var PaymentCloud $token
 * @var boolean $saveCard
 * @var boolean $autoPay
 */

use Dryharder\Gateway\Models\PaymentCloud;


?>

<?php

if ($token) {

    ?>

    <button class="btn btn-primary select-card-item" data-type="token">
        <?= trans('main.use old card') ?>
        <br>
        <?= $token->card_type ?>
        ***<?= substr($token->card_pan, -4) ?>
    </button>

<?php

}

?>

<button class="btn btn-primary select-card-item" data-type="card">
    <?= trans('main.use other card') ?>
</button>

<p class="select-card-item-checkbox">
    <input id="save-card-modal" type="checkbox" <?= $saveCard ? ' checked="checked"' : '' ?> class="checkbox-save-card">
    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?= trans('main.what is it') ?>"></i>
    <label for="save-card-modal"><?= trans('main.AFTER_PAYMENT_SAVE_CARD') ?></label>
</p>

<p class="auto-pay-item-checkbox <?= $saveCard ? 'active': 'disabled' ?>">
    <input id="save-autopay-modal" type="checkbox" <?= $autoPay ? ' checked="checked"' : '' ?> class="checkbox-autopay-card" <?= !$saveCard ? 'disabled': '' ?>>
    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?= trans('main.what is it') ?>"></i>
    <label for="save-autopay-modal">
        <?= trans('main.AUTO_PAYMENT_SAVE_CARD') ?>
    </label>
</p>

<p class="select-card-item-info mt-40"><?= trans('main.card security info') ?></p>

<p style="text-align: center;"><?= trans('main.or') ?></p>

<button class="btn btn-primary select-card-item yandex" data-type="yandex">
    <?= trans('main.use yandex money') ?>
</button>

<p class="save-card-info" style="display: none;"><?= trans('main.AFTER_PAYMENT_SAVE_CARD_T') ?></p>
<p class="autopay-info" style="display: none;"><?= trans('main.AUTO_PAYMENT_SAVE_CARD_T') ?></p>
<p class="autopay-info-off" style="display: none;"><?= trans('main.AUTO_PAYMENT_OFF_T') ?></p>