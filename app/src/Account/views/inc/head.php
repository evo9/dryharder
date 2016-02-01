<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= trans('main.title.page') ?></title>
<link media="all" rel="stylesheet" href="/assets/css/app.min.css">
<link media="all" rel="stylesheet" href="/assets/css/account.css?v=<?= $v ?>">
<!--<link media="all" rel="stylesheet" href="style.css">-->
<!--[if lt IE 9]>
<link rel="stylesheet" href="/assets/css/ie.min.css">
<script type="text/javascript" src="/assets/js/ie.js"></script>
<![endif]-->
<script type="text/javascript">
    <?php
    echo 'var Trans = ' . json_encode(require(app_path() . '/lang/' . App::getLocale() . '/js.php'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';';
    echo 'var LANG = "' . App::getLocale() . '";';
    echo 'API_BASE_URL = "' . Config::get('agbis.api.self') . '";';
    ?>
    function trans(text){
        return Trans[text] || text;
    }
</script>