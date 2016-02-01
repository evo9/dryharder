<?php

/**
 * @var string $cardInfo
 * @var string $saveCard
 * @var string $invite_url
 */

?>

    <div class="container" id="account-forms">
        <div class="row">
            <div class="col-xs-12">

                <div class="heading-block">
                    <h3><?= trans('main.MY_ACCOUNT_INFO') ?></h3>
                </div>

                <ul class="accordion-account">

                    <li id="registration_info">
                        <a class="opener" href="#"><i class="fa fa-bolt" style="width: 22px;"></i><?= trans('main.REGISTRATION_INFORMATION_T') ?></a>
                        <div class="slide">

                            <form action="#" class="registration-form data-form">
                                <fieldset>
                                    <div class="row-holder">
                                        <div class="holder">
                                            <div class="cell">
                                                <div class="label-holder">
                                                    <label for="f01"><i class="fa fa-user"></i><?= trans('main.USERNAME') ?></label>
                                                </div>
                                                <div class="input-holder">
                                                    <input id="f01" type="text" placeholder="" name="name">
                                                </div>
                                            </div>
                                            <div class="cell">
                                                <div class="label-holder">
                                                    <label for="f02"><i class="fa fa-phone-square"></i><?= trans('main.PHONE_NUMBER') ?></label>
                                                </div>
                                                <div class="input-holder">
                                                    <input id="f02" type="tel" placeholder="+7 000 000 0000" name="phone2">
                                                </div>
                                            </div>
                                            <div class="cell">
                                                <div class="label-holder">
                                                    <label for="f03"><i class="fa fa-mobile"></i><?= trans('main.MOBILE_NUMBER') ?></label>
                                                </div>
                                                <div class="input-holder">
                                                    <input id="f03" type="tel" placeholder="+7 000 000 0000" name="phone">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row-holder">
                                        <div class="holder">
                                            <div class="cell">
                                                <div class="label-holder">
                                                    <label for="f08"><i class="fa fa-envelope"></i><?= trans('main.EMAIL_ADDRESS') ?></label>
                                                </div>
                                                <div class="input-holder">
                                                    <input id="f08" type="email" placeholder="example@example.com" name="email">
                                                </div>
                                            </div>
                                            <div class="cell two-cell">
                                                <div class="label-holder">
                                                    <label for="f09"><i class="fa fa-home"></i><?= trans('main.ADDRESS') ?></label>
                                                </div>
                                                <div class="input-holder">
                                                    <input id="f09" type="text" placeholder="" name="address">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row-holder">
                                        <div class="holder">
                                            <div class="cell">
                                                <button class="btn btn-primary" data-loading-text="<?= trans('main.saving') ?>"><?= trans('main.SAVE_CHANGES') ?><i class="fa fa-floppy-o"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>

                        </div>
                    </li>

                    <li id="payment_info">
                        <a class="opener" href="#"><i class="fa fa-rub" style="width: 22px;"></i><?= trans('main.PAYMENT_INFO') ?></a>
                        <div class="slide">
                            <form action="#" class="registration-form pay-form">
                                <fieldset>
                                    <?php if($cardInfo){ ?>
                                        <div class="row-holder info-card-in-account">
                                            <div class="holder">
                                                <h4><?= trans('main.Last card') ?></h4>
                                                <?= $cardInfo ?>
                                            </div>
                                        </div>
                                        <div class="row-holder info-card-in-account">
                                            <div class="holder">
                                                <div class="cell">
                                                    <button class="btn btn-primary save-card-remove" data-loading-text="<?= trans('main.deleting') ?>"><?= trans('main.DELETE CARD') ?><i class="fa fa-floppy-o"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="row-holder personal-card-settings">
                                        <div class="holder">

                                            <div>
                                                <input id="save-card-account" type="checkbox" <?= $saveCard ? ' checked="checked"' : '' ?> class="checkbox-save-card">
                                                <label for="save-card-account" class="label-save-card" style="margin-top: 10px;"><?= trans('main.AFTER_PAYMENT_SAVE_CARD') ?></label>
                                            </div>
                                            <div style="clear: both;"></div>

                                            <div>
                                                <input id="save-autopay-account" type="checkbox" <?= $saveCard ? ' checked="checked"' : '' ?> class="checkbox-autopay-card">
                                                <label for="save-autopay-account" class="save-autopay-modal" style="margin-top: 10px;"><?= trans('main.AUTO_PAYMENT_SAVE_CARD') ?></label>
                                            </div>
                                            <div style="clear: both;"></div>

                                            <p class="pay-form-comment"><small><?= trans('main.card security info') ?></small></p>

                                            <p class="save-card-info" style="display: none;"><?= trans('main.AFTER_PAYMENT_SAVE_CARD_T') ?></p>
                                            <p class="autopay-info" style="display: none;"><?= trans('main.AUTO_PAYMENT_SAVE_CARD_T') ?></p>
                                            <p class="autopay-info-off" style="display: none;"><?= trans('main.AUTO_PAYMENT_OFF_T') ?></p>

                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </li>

                    <li id="password_settings">
                        <a class="opener" href="#"><i class="fa fa-lock" style="width: 22px;"></i><?= trans('main.PASSWORD_SETTINGS') ?></a>
                        <div class="slide">
                            <form action="#" class="registration-form password-form">
                                <fieldset>
                                    <div class="row-holder">
                                        <div class="holder">
                                            <div class="cell">
                                                <div class="label-holder">
                                                    <label for="f15"><?= trans('main.CURRENT_PASSWORD') ?></label>
                                                </div>
                                                <div class="input-holder">
                                                    <input id="f15" type="password" placeholder="" name="password">
                                                </div>
                                            </div>
                                            <div class="cell">
                                                <div class="label-holder">
                                                    <label for="f16"><?= trans('main.NEW_PASSWORD') ?></label>
                                                </div>
                                                <div class="input-holder">
                                                    <input id="f16" type="password" placeholder="" name="password1">
                                                </div>
                                            </div>
                                            <div class="cell">
                                                <div class="label-holder">
                                                    <label for="f17"><?= trans('main.REPEAT_NEW_PASSWORD') ?></label>
                                                </div>
                                                <div class="input-holder">
                                                    <input id="f17" type="password" placeholder="" name="password2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row-holder">
                                        <div class="holder">
                                            <div class="cell">
                                                <button class="btn btn-primary" data-loading-text="<?= trans('main.saving') ?>"><?= trans('main.SAVE') ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div>
