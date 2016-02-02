<div class="modal fade" id="flashMessage" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="padding: 10px 10px 10px;">
            <button type="button" class="close" data-dismiss="modal">
                <i class="fa fa-times-circle"></i>
            </button>
            <div class="heading">
                <h3 class="message_title"><?= trans('flashes.add_card.success_title'); ?></h3>
            </div>
            <div class="row-holder">
                <p><?= trans('flashes.add_card.success_content'); ?></p>
            </div>
            <div class="checkboxes">
                <input type="checkbox" name="autopay" id="autopay_input" hidden>
                <label for="autopay_input"><?= trans('flashes.add.card.check_autopay'); ?></label>
            </div>
            <div class="action_buttons text-center">
                <button class="btn btn-dh-green" onclick="payFinish()"><?= trans('main.ok'); ?></button>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
