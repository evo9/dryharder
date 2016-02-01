<?php
/**
 * @var string $invite_url
 */

?>

<div class="socials-links">
    <a href="http://vk.com/share.php?url=<?= urlencode($invite_url) ?>" target="_blank"><i class="fa fa-vk"></i> VKontakte</a>
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($invite_url) ?>" target="_blank"><i class="fa fa-facebook"></i> Facebook</a>
    <a href="https://twitter.com/home?status=<?= urlencode('Dry Harder: ' . trans("main.ShareText") . ' / ' . $invite_url) ?>" target="_blank"><i class="fa fa-twitter"></i> Twitter</a>
</div>
