<?php

$title = "Dry Harder";
$image = url('/images/logo-small.png');
$description = trans('main.metaDescription');
$url = Request::url();
$lang = App::getLocale();
$page = $lang == 'ru' ? '' : $lang . '.html';

?><!DOCTYPE html>
<html xmlns:og="http://ogp.me/ns#">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="refresh" content="1;url=<?= url('/' . $page . '#invite') ?>" />
    <title>Dry Harder</title>

    <meta property="og:title" content="<?= $title ?>" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?= $url ?>" />
    <meta property="og:image" content="<?= $image ?>" />
    <meta property="og:description" content="<?= $description ?>" />
    <meta property="og:site_name" content="<?= $title ?>" />


    <link rel="image_src" href="<?= url('/images/logo.png') ?>" />

    <meta itemprop="name" content="<?= $title ?>" />
    <meta itemprop="description" content="<?= $description ?>" />
    <meta itemprop="image" content="<?= $image ?>" />

    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="<?= $title ?>" />
    <meta name="twitter:title" content="<?= $title ?>">
    <meta name="twitter:description" content="<?= $description ?>" />
    <meta name="twitter:creator" content="<?= $title ?>" />
    <meta name="twitter:image:src" content="<?= $image ?>" />
    <meta name="twitter:domain" content="dryharder.me" />

    <style>
        body {
            padding: 100px;
            position: relative;
            background: url(/images/bg-start-section.png) 50% 0/200px 200px;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        }

        h1 {
            font-weight: normal;
            font-size: 30px;
        }

        p {
            font-weight: normal;
            font-size: 20px;
        }

        a {
            display: inline-block;
            vertical-align: top;
            text-decoration: none;
            color: #000;
            outline: 0 !important;
            padding-bottom: 10px;
            border: none;
            -webkit-transition: none;
            -moz-transition: none;
            transition: none;
        }

        a:hover, a:focus {
            color: #2dd982;
            text-decoration: none;
            outline: none !important;
        }

    </style>

</head>
<body>
<div style="margin: 0 auto; width: 400px; text-align: center;">
    <img src="<?= $image ?>" />

    <h1><?= $title ?></h1>

    <div><p><?= $description ?></p></div>
    <br> <br> <a href="<?= url('/#invite') ?>"><?= trans('main.welcomePageLink') ?></a>
</div>
</body>
</html>