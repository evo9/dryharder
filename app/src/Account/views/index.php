<?php
use Dryharder\Gateway\Models\PaymentCloud;

/**
 * @var array|null $promo
 * @var array $user
 * @var PaymentCloud $token
 * @var string $agbisKey
 * @var boolean $saveCard
 * @var string $invite_url
 */

$cardInfo = null;
if ($token) {
	$cardInfo = '<i class="fa fa-credit-card"></i> ' . $token->card_type . ' ***' . substr($token->card_pan, -4);
}

$v = 19;

?>
<!DOCTYPE html>
<html>
<head>
	<?= View::make('ac::inc.head', compact('v')) ?>
</head>
<body>
<div id="wrapper">

	<header id="header">
		<?= View::make('ac::inc.header', compact('user', 'promo', 'cardInfo')) ?>
	</header>

	<main id="main" role="main">
		<div class="add-mobile-informations visible-xs">
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<div class="info-holder">
							<span class="info header-card-item"><?= $cardInfo ?></span>
							<span class="info"><i class="percentage">%</i> <?= $promo['discountText'] ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>


<!-- табы -->


		<div class="account-section">
			<div class="statictics-container">
				<div class="container">
					<div class="row">
						<div class="col-md-3 col-sm-4 col-xs-12">
							<strong class="account-title"><i class="fa fa-bar-chart"></i> <?= trans('main.Your account statistics') ?> </strong>
						</div>
						<div class="col-md-9 col-sm-8 col-xs-12">
							<ul class="orders-list">
								<li>
									<strong class="title"><i class="fa fa-shopping-cart"></i> <span><?= $user['orders_total'] ?></span></strong>
									<a href="#" onclick="return false;" class="link"><?= trans('main.Total Orders') ?></a>
								</li>
								<li>
									<strong class="title"><i class="fa fa-thumbs-o-up"></i> <span class="link orders-qnt"><?= $user['order_qnt'] ?></span></strong>
									<a href="#" onclick="return false;" class="link"><?= trans('main.Ready Orders') ?></a>
								</li>
								<li>
									<strong class="title"><i class="fa fa-rub"></i> <span class="link bonuses-qnt"></span></strong>
									<a href="#" onclick="return false;" class="link"><?= trans('main.Bonuses') ?></a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="orders-tabset-container">
				<div class="container">
					<div class="row">
						<div class="col-xs-12">
							<!-- orders-tabset of the page -->
							<nav class="orders-tabset">
								<div class="mask">
									<ul>
										<li><a href="#tab3_2" class="tab tab-create-order"><i class="fa fa-pencil"></i> <?= trans('main.Create order') ?> </a></li>
										<li><a href="#tab1_2" class="tab"><i class="fa fa-shopping-cart"></i> <?= trans('main.Current orders') ?></a></li>
										<li><a href="#tab2_2" class="tab"><i class="fa fa-list-alt"></i> <?= trans('main.Order history') ?> </a></li>
<!--										<li><a href="#tab3_1" class="tab"><i class="fa fa-inbox"></i> --><?//= trans('main.Subscriptions') ?><!-- </a></li>-->
										<li><a href="#tab7_2" class="add-tab tab"><i class="fa fa-user"></i> <?= trans('main.My Account') ?></a></li>
										<li><a href="#tab7_3" class="add-tab tab" style="min-width: 160px;"><i class="fa fa-share-square-o"></i> <?= trans('main.Share Friends') ?></a></li>
										<li class="last"><a href="#tab8_2" class="add-tab tab"><i class="fa fa-phone-square"></i><?= trans('main.Callback') ?></a></li>
									</ul>
								</div>
								<a href="#" class="btn-prev visible-sm visible-xs"><?= trans('main.previous') ?></a>
								<a href="#" class="btn-next visible-sm visible-xs"><?= trans('main.next') ?></a>
							</nav>
							<div class="overlay-loading-orders">
								<div>
									<p class="preloader"></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

<!-- текущие заказы -->

		<div class="orders-tab-content add-price-style" style="min-height: 800px;">
			<div id="tab1_2" class="tab-box">
				<!-- twocolumns of the page -->
				<div class="twocolumns hidden-sm hidden-xs">
					<div class="container">
						<div class="row">
							<div class="content col-xs-9">
								<div class="orders-results-table current-orders"></div>
							</div>
							<div class="col-xs-3 ticket-fix">
								<div class="table-tabset-holder">
									<div id="table-tab01">
										<aside class="aside">
                                            <div class="button-holder checkout" style="display: none;">
                                                <a href="#" class="btn btn-primary"><?= trans('main.Checkout') ?> <i class="fa fa-shopping-cart"></i></a>
                                            </div>
											<div class="orders-table current-orders"></div>
										</aside>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-xs-12">
							<ul class="orders-accordion visible-sm visible-xs current-orders"></ul>
						</div>
					</div>
				</div>
			</div>

<!-- история заказов -->

			<div id="tab2_2" class="tab-box">
				<div class="twocolumns hidden-sm hidden-xs">
					<div class="container">
						<div class="row">
							<div class="content col-xs-9">
								<div class="orders-results-table history-orders"></div>
							</div>
							<div class="col-xs-3 ticket-fix">
								<div class="table-tabset-holder">
									<div id="table-tab001">
										<aside class="aside">
											<div class="orders-table history-orders"></div>
										</aside>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-xs-12">
							<ul class="orders-accordion visible-sm visible-xs history-orders"></ul>
						</div>
					</div>
				</div>
			</div>

            <div id="tab3_2" class="tab-box">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 create-order-form-content"></div>
                    </div>
                </div>
            </div>

            <div id="tab7_2" class="tab-box">
			    <?= View::make('ac::inc.registration-form', compact('cards')) ?>
            </div>

            <div id="tab7_3" class="tab-box">
			    <?= View::make('ac::inc.invite', compact('invite_url')) ?>
            </div>

            <div id="tab3_1" class="tab-box">
			    <?= View::make('ac::inc.subscriptions') ?>
            </div>

			<div id="tab8_2" class="tab-box">
				<div class="container">
					<div class="row">
						<div class="col-md-4">
							<div class="text-container">
								<p><?= trans('main.Callback text') ?></p>
								<p><a href="tel:+74956665607">+7 495 666 56 07</a></p>
								<p><a href="#" data-target="feedbackForm">info@dryharder.me</a></p>
							</div>
						</div>
                        <div class="col-md-8">

                            <form action="feedback/message/create" class="lite-feedback" onsubmit="return false;">
                                <input type="hidden" name="name" value="<?= $user['name'] ?>">
                                <input type="hidden" name="phone" value="<?= $user['phone'] ?>">
                                <input type="hidden" name="email" value="<?= $user['email'] ?>">
                                <div class="row">
                                    <textarea name="text" placeholder="<?= trans('main.message') ?>" cols="10" rows="4" style="width: 100%; height: 200px;"></textarea>
                                </div>
                                <div class="row" style="margin-top: 10px;">
                                    <div class="pull-right">
                                        <button type="submit" value="" class="btn btn-primary" data-loading-text="<?= trans('main.sending') ?>"><?= trans('main.send') ?></button>
                                    </div>
                                </div>
                            </form>

                        </div>
					</div>
				</div>
			</div>

		</div>
	</main>

	<footer id="footer" class="inner-footer">
		<div class="container">
			<div class="row">
				<div class="col-sm-3 col-xs-12">
					<p>&copy; 2014 Dry Harder</p>
				</div>
				<div class="col-sm-3 col-xs-12">
					<a href="#" onclick="return false;" class="link show-license-link"><?= trans('main.Terms') ?></a>
				</div>
				<div class="col-sm-3 col-xs-12">
					<a href="#" onclick="return false;" class="link" data-target="feedbackForm"><?= trans('main.Need help') ?>?</a>
				</div>
			</div>
		</div>
	</footer>

	<div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content style03">
				<div class="heading">
					<button type="button" class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></button>
					<h3><?= trans('main.Order details') ?></h3>
				</div>
				<!-- orders-table of the page -->
				<div class="orders-table"></div>
				<div class="button-holder checkout">
					<a href="#" class="btn btn-primary"><?= trans('main.Checkout') ?> <i class="fa fa-shopping-cart"></i></a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="select-pay-card" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content style03">
				<div class="heading">
					<button type="button" class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></button>
					<h3><?= trans('main.modal select card') ?></h3>
				</div>
				<div class="card-table">

				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myLicense" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content" style="padding: 10px 10px 10px;">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times-circle"></i>
				</button>
				<div class="heading">
					<h3><?= trans('main.Terms') ?></h3>
				</div>
				<div class="row-holder"></div>
				<button type="button" class="btn btn-primary pull-right" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>

	<div class="modal fade" id="reviewSurvey" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content" style="padding: 10px 10px 10px;">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times-circle"></i>
				</button>
				<div class="row-holder">
                    <p><strong><?= trans('flashes.review.title') ?>:</strong></p>
                    <div style="width: 170px; height: 50px; margin: 0 auto;" class="stars">
                        <a href="#" onclick="return false;" data-num="1" class="star"><i class="fa fa-star-o"></i> </a>
                        <a href="#" onclick="return false;" data-num="2" class="star"><i class="fa fa-star-o"></i> </a>
                        <a href="#" onclick="return false;" data-num="3" class="star"><i class="fa fa-star-o"></i> </a>
                        <a href="#" onclick="return false;" data-num="4" class="star"><i class="fa fa-star-o"></i> </a>
                        <a href="#" onclick="return false;" data-num="5" class="star"><i class="fa fa-star-o"></i> </a>
                    </div>
                    <p><?= trans('flashes.review.comment') ?>:</p>
                    <!--suppress HtmlFormInputWithoutLabel -->
                    <textarea></textarea>
                    <div class="clearfix"></div>
                    <div class="button-holder">
                        <button class="btn btn-primary pull-right" type="button" data-loading-text="<?= trans('main.sending') ?>"><?= trans('main.send') ?></button>
                    </div>
                    <div class="clearfix"></div>
                </div>
			</div>
            <div class="clearfix"></div>
		</div>
	</div>

	<div class="modal fade" id="order-detail-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content style03">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times-circle"></i>
				</button>
				<div class="heading">
					<h3><?= trans('main.clothes description') ?></h3>
				</div>
				<div class="detail-order-place">

				</div>
				<div class="clear clearfix"></div>
			</div>
		</div>
	</div>

</div>

<div class="alert-template">
    <div class="alert alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4></h4>
	    <button class="btn btn-primary" data-dismiss="alert">ОК</button>
    </div>
</div>

<div class="alert-template-confirm">
    <div class="alert alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4></h4>
	    <button class="btn btn-primary confirm-alert-button" data-dismiss="alert"></button>
	    <button class="btn btn-primary" data-dismiss="alert"><?= trans('main.Cancel') ?></button>
    </div>
</div>



<script type="text/javascript">
    var CID = <?= $user['id'] ?>;
    var YAM_URL = '<?= Config::get('cloud.yam.url') ?>';
    var YAM_SID = '<?= Config::get('cloud.yam.shopId') ?>';
    var YAM_SCID = '<?= Config::get('cloud.yam.scid') ?>';
</script>
<script type="text/javascript" src="/assets/js/src.min.js"></script>
<script type="text/javascript" src="/assets/js/order-form.js?v=<?= $v ?>"></script>
<script type="text/javascript" src="/assets/js/account.js?v=<?= $v ?>"></script>
<script src="https://widget.cloudpayments.ru/bundles/cloudpayments"></script>
<?= View::make('ac::inc.feedback', compact('user', 'promo', 'cardInfo')) ?>

<?php if(!empty($errors) && $errors->any()){ ?>
    <script type="text/javascript">
        $(document).ready(function(){
            AppAccount.alertError('<?= $errors->first() ?>')
        });
    </script>
<?php } ?>

</body>
</html>