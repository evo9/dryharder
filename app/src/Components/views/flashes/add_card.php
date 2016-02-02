<div class="modal fade" id="flashMessage" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="padding: 10px 10px 10px;">
            <button type="button" class="close" data-dismiss="modal">
                <i class="fa fa-times-circle"></i>
            </button>
            <div class="heading">
                <h3 class="message_title"><?= trans('flashes.add_card.title'); ?></h3>
            </div>
            <div class="row-holder">
                <p class="text-center"><?= trans('flashes.add_card.content'); ?></p>
            </div>
            <div class="action_buttons text-center">
                <button class="btn btn-dh-green" onclick="addCard()"><?= trans('main.next'); ?></button>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
