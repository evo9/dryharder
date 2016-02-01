<?php

/**
 * @var array $user
 */

/** @noinspection PhpIncludeInspection */

$html = file_get_contents(app_path() . '/../static/templates/index/modal/feedbackForm.html');
$lang = file_get_contents(app_path() . '/../static/lang/gulp/' . App::getLocale() . '.js');
$json = preg_replace('/^.*\{(.*)\}.*$/sm', '{$1}', $lang);
$json = preg_replace('/\/\/[^\n]+[\n\r]+/', '', $json);
$json = preg_replace('/([a-zA-Z0-9_-]+):/', '"$1":', $json);
$json = preg_replace('/[\n\r\t]+/', '', $json);
$json = trim($json);
$lang = json_decode($json);

foreach($lang as $key => $value){
    $html = preg_replace('/\@\@' . $key . '([^a-zA-Z0-9]+)/', $value . '$1', $html);
}
echo $html;

?>
<script type="text/javascript">
    $(document).ready(function(){
        <?php
        /** @noinspection PhpIncludeInspection */
        require (app_path() . '/../static/js/project/inc/feedback.js');
        ?>
        initFeedbackAjaxSend({
            name: '<?= e($user['name']) ?>',
            phone: '<?= e($user['phone']) ?>',
            email: '<?= e($user['email']) ?>'
        });
    });
</script>
