<?php

/**
 * @var string $invite_url
 * @var integer $qnt
 */

$qnt = empty($qnt) ? 0 : $qnt;

?>
<!--suppress HtmlFormInputWithoutLabel -->
<div class="container" id="share-links">
    <div class="row">
        <div class="col-xs-12">

            <p><?= trans('main.SHARE_FRIEND_INFO') ?></p>

            <div>
                <input type="text" class="share-url" value="<?= $invite_url ?>" style="width: 100%;">
            </div>

            <p style="margin-top: 40px;"><?= trans('main.SHARE_FRIEND_SOCIALS') ?></p>

            <?= View::make('ac::inc.social-links', compact('invite_url')) ?>

            <?php if($qnt > 0){ ?>
                <p style="margin-top: 40px;"><?= trans('main.SHARE_STATISTIC', ['qnt' => $qnt]) ?></p>
            <?php } ?>

        </div>
    </div>
</div>
<style type="text/css">
    #share-links div.socials-links {
        font-size: 26px;
        margin-bottom: 40px;
    }
    #share-links div.socials-links i.fa {
        font-size: 44px;
        color: #2dd982;
    }
    #share-links div.socials-links a {
        margin-right: 30px;
        margin-bottom: 20px;
        vertical-align: bottom;
    }
</style>
