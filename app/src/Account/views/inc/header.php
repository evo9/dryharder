<?php

use Dryharder\Gateway\Models\PaymentCloud;

/**
 * @var array|null $promo
 * @var array $user
 * @var PaymentCloud $token
 * @var string $cardInfo
 */

$mainPage = Config::get('app.url');
$langReplace = App::getLocale() == 'ru' ? 'index' : App::getLocale();
$mainPage = str_replace('#lang#', $langReplace, $mainPage);
?>

<!-- inner-header -->
<div class="inner-header hidden-xs" id="inner-header">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6 small-logo">
                <a href="<?= $mainPage ?>" class="inner-small-logo" data-toggle="tooltip" data-placement="bottom" title="<?= trans('main.Link to index') ?>"><i class="icon-svg9"></i></a>
                <strong class="title"><?= trans('main.Hello') ?>,<br><span class="mark01"><?= $user['name'] ?>!</span></strong>
            </div>
            <div class="col-md-3 col-sm-3">
                <span class="card-info header-card-item" data-toggle="tooltip" data-placement="bottom" title="<?= trans('main.Last card') ?>"><?= $cardInfo ?></span>
            </div>
            <div class="col-md-3 col-sm-3">
						<span class="info" data-toggle="tooltip" data-placement="bottom" title="<?= trans('main.Discount') ?>">
							<i>%</i> <?= $promo['discountText'] ?>
						</span>
            </div>
            <div class="col-md-3 col-sm-12 waiting-for-logout">
                <div class="language-form">
                    <a href="#" onclick="return false;" class="lang-href<?= App::getLocale() == 'ru'?  ' active': '' ?>" data-lang="ru">Ru</a>
                    <a href="#" onclick="return false;" class="lang-href<?= App::getLocale() == 'en'?  ' active': '' ?>" data-lang="en">En</a>
                </div>
                <ul class="top-nav">
                    <li><a href="#" data-target="feedbackForm"><i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="<?= trans('main.I Need Help') ?>"></i></a></li>
                    <li><a href="#" class="red-style signout-link" data-toggle="tooltip" data-placement="bottom" title="<?= trans('main.Sign out') ?>"><i class="fa fa-power-off"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- mobile-header of the page -->
<div class="mobile-header visible-xs inner-style" id="inner-mobile-header">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <a href="#" class="mobile-logo"><i class="icon-svg5"></i></a>
                <strong class="title">Hello <span class="mark01"><?= $user['name'] ?>!</span></strong>
                <div class="mobile-navigation">
                    <a href="#" class="opener"><i class="fa fa-bars"></i></a>
                    <div class="drop">
                        <div class="frame">
                            <ul class="main-nav">
                                <li><a href="#"><i class="fa fa-user"></i> <?= trans('main.My Account') ?></a></li>
<!--                                <li><a href="#"><i class="fa fa-phone-square"></i>= trans('main.Call Back') </a></li>-->
<!--                                <li><a href="#"><i class="fa fa-map-marker"></i>= trans('main.Our Locations') </a></li>-->
                                <li><a href="#" data-target="feedbackForm"><i class="fa fa-question-circle"></i> <?= trans('main.I Need Help') ?></a></li>
                            </ul>
                            <form action="#" class="language-form">
                                <fieldset>
                                    <div class="select-holder">
                                        <select class="mobile">
                                            <option value="ru">Русский</option>
                                            <option value="en">English</option>
                                        </select>
                                    </div>
                                </fieldset>
                            </form>
                            <div class="link-holder">
                                <a href="#" class="signout-link waiting-for-logout"><i class="fa fa-power-off"></i> <?= trans('main.Sign out') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>