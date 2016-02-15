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