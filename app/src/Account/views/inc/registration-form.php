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
                            <div class="inner">
                                <div class="card_action_button">
                                    <button class="btn btn-primary" onclick="getFlashMessage('add_card_description');"><?php echo trans('main.add_new_card') ?> <i class="fa fa-plus"></i></button>
                                </div>
                                <ul id="account_card_list">
                                    <li class="heading">
                                        <span class="select_card"></span>
                                        <span class="card_num"><?php echo trans('main.card_num'); ?></span>
                                        <span class="card_autopay"><?php echo trans('main.card_autopay'); ?></span>
                                    </li>
                                    <?php foreach ($cards as $card) : ?>
                                        <li>
                                        <span class="select_card">
                                            <span class="label" data-payment="<?php echo $card['payment_id'] ?>"></span>
                                        </span>
                                            <span class="card_num"><?php echo $card['card_pan']; ?></span>
                                        <span class="card_autopay">
                                            <span class="label <?php echo $card['autopay'] ? 'checked' : ''; ?>" data-payment="<?php echo $card['payment_id'] ?>"></span>
                                        </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="card_action_button">
                                    <button class="btn btn-primary" onclick="deleteCard($(this))" data-loading-text="<?= trans('main.deleting') ?>"><?php echo trans('main.DELETE CARD') ?> <i class="fa fa-trash"></i></button>
                                </div>

                                <div id="payment_info_description">
                                    <?php echo trans('main.payment_info'); ?>
                                </div>
                            </div>
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
