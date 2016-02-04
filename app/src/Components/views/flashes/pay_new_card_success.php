<div class="modal fade" id="flashMessage" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 600px;">
        <div class="modal-content" style="padding: 10px 10px 10px;">
            <button type="button" class="close" data-dismiss="modal">
                <i class="fa fa-times-circle"></i>
            </button>
            <div class="heading">
                <h3 class="message_title"><?php echo trans('flashes.add_card.success_title'); ?></h3>
            </div>
            <div class="row-holder">
                <p class="text-center"><?php echo trans('flashes.add_card.success_content'); ?></p>
            </div>
            <div class="checkboxes">
                <input type="checkbox" name="save_card" id="save_card_input" value="1" hidden>
                <label for="save_card_input"><?php echo trans('flashes.add_card.save_card'); ?></label>
                <input type="checkbox" name="autopay" id="autopay_input" value="1" hidden>
                <label for="autopay_input"><?php echo trans('flashes.add_card.check_autopay'); ?></label>
            </div>
            <div class="action_buttons text-center">
                <button class="btn btn-dh-green" onclick="payFinish()"><?php echo trans('main.ok'); ?></button>
            </div>
            <div class="small_desc"><?php echo trans('flashes.add_card.not_save_data'); ?></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
