<?php

/**
 * @var string $title
 * @var string $content
 */

?>

<!--suppress ALL -->
<p><?= $content ?></p>

<script type="text/javascript">

    var $modal = $('#flashMessage');
    $modal.find('.heading h3').html('<?= $title ?>');

</script>

<style type="text/css">
    #flashMessage .modal-dialog {
        box-shadow: 0 0 10px grey;
    }
</style>
