<?php

function truncate($str, $size = 40, $end = '...')
{
    if (mb_strlen($str) <= $size) {
        return $str;
    }

    $str = mb_substr($str, 0, mb_strrpos(mb_substr($str, 0, $size), ' ')) . $end;

    return $str;

}