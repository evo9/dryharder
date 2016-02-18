<div class="modal fade" id="prepayment" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">
                <i class="fa fa-times-circle"></i>
            </button>
            <div class="heading">
                <h3 class="message_title"><?php echo trans('pay.prepayment.title'); ?></h3>
            </div>
            <div class="row-holder">
                <div class="cards_wrap">
                    <div class="selected">
                        <span><?php echo $selectedCard['card_pan']; ?></span>
                        <input type="hidden" name="payment" value="<?php echo $selectedCard['payment_id']; ?>">
                    </div>
                    <ul>
                        <li data-payment="-1"><?php echo trans('pay.prepayment.new_card'); ?></li>
                        <?php if (count($cards) > 0) : ?>
                            <?php foreach ($cards as $card) : ?>
                                <li data-payment="<?php echo $card['payment_id'] ?>"><?php echo $card['card_pan']; ?></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="checkboxes">
                <input type="checkbox" name="yandex" id="yandex_input" value="1" hidden>
                <label for="yandex_input">
                    <span><?php echo trans('pay.prepayment.use'); ?></span>
                    <span class="yandex_money"></span>
                </label>
            </div>
            <div class="action_buttons text-center">
                <button class="btn btn-dh-green" data-load="<?php echo trans('main.payment_process'); ?>"><?php echo trans('main.next'); ?></button>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
