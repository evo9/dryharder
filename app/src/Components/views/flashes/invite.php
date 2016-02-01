<?php

/**
 * @var string $title
 * @var string $content
 * @var string $invite_url
 */

?>

<!--suppress ALL -->
<p><?= $content ?></p>

<div>
    <input type="text" class="share-url" value="<?= $invite_url ?>" style="width: 100%;">
</div>

<div style="margin: 10px; padding: 10px;">
    <?= View::make('ac::inc.social-links', compact('invite_url')) ?>
</div>

<script type="text/javascript">

	var $modal = $('#flashMessage');
	$modal.find('.heading h3').html('<?= $title ?>');
    var $input = $modal.find('input.share-url');
    $input.focus(function() { $(this).select(); } );
    $input.trigger('focus');

</script>

<style type="text/css">
    #flashMessage .modal-dialog {
        box-shadow: 0 0 10px grey;
    }
    #flashMessage div.socials-links {
        font-size: 18px;
        margin-bottom: 40px;
    }
    #flashMessage div.socials-links i.fa {
        font-size: 30px;
        color: #2dd982;
    }
    #flashMessage div.socials-links a {
        margin-right: 10px;
        margin-bottom: 10px;
        vertical-align: bottom;
    }
</style>
