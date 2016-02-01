<?php



$fromHours = [
    '0700' => '07.00-12.00',
    '1200' => '12.00-17.00',
    '1700' => '17.00-23.00',
];

$t = array();
for ($i = 1; $i <= 30; $i++) {
    $t[$i] = strtotime('+' . $i . ' day');
}
$dayOfWeekList = array(
    1 => trans('request.в понедельник'),
    2 => trans('request.во вторник'),
    3 => trans('request.в среду'),
    4 => trans('request.в четверг'),
    5 => trans('request.в пятницу'),
    6 => trans('request.в субботу'),
    0 => trans('request.в воскресенье'),
);
$dayOfWeekListShort = array(
    1 => trans('request.пн'),
    2 => trans('request.вт'),
    3 => trans('request.ср'),
    4 => trans('request.чт'),
    5 => trans('request.пт'),
    6 => trans('request.сб'),
    0 => trans('request.вс'),
);

$monthNames = array(
    1  => trans('request.января'),
    2  => trans('request.февраля'),
    3  => trans('request.марта'),
    4  => trans('request.апреля'),
    5  => trans('request.мая'),
    6  => trans('request.июня'),
    7  => trans('request.июля'),
    8  => trans('request.августа'),
    9  => trans('request.сентября'),
    10 => trans('request.октября'),
    11 => trans('request.ноября'),
    12 => trans('request.декабря'),
);


$after = trans('request.после') . ' ' . date('j', $t[15]) . ' ' . $monthNames[date('n', $t[15])];

$fromNames = array(
    trans('request.сегодня'),
    trans('request.завтра') . ' (' . $dayOfWeekList[date('w', $t[1])] . ')',
    trans('request.послезавтра') . ' (' . $dayOfWeekList[date('w', $t[2])] . ')',
    $dayOfWeekList[date('w', $t[3])] . ', ' . date('j', $t[3]) . ' ' . $monthNames[date('n', $t[3])],
    $dayOfWeekList[date('w', $t[4])] . ', ' . date('j', $t[4]) . ' ' . $monthNames[date('n', $t[4])],
    $dayOfWeekList[date('w', $t[5])] . ', ' . date('j', $t[5]) . ' ' . $monthNames[date('n', $t[5])],
    $dayOfWeekList[date('w', $t[6])] . ', ' . date('j', $t[6]) . ' ' . $monthNames[date('n', $t[6])],
    $dayOfWeekList[date('w', $t[7])] . ', ' . date('j', $t[7]) . ' ' . $monthNames[date('n', $t[7])],
    $dayOfWeekList[date('w', $t[8])] . ', ' . date('j', $t[8]) . ' ' . $monthNames[date('n', $t[8])],
);

$fromNamesShort = array(
    trans('request.сегодня'),
    trans('request.завтра') . ' (' . $dayOfWeekListShort[date('w', $t[1])] . ')',
    trans('request.послезавтра') . ' (' . $dayOfWeekListShort[date('w', $t[2])] . ')',
    $dayOfWeekListShort[date('w', $t[3])] . ' ' . date('j', $t[3]),
    $dayOfWeekListShort[date('w', $t[4])] . ' ' . date('j', $t[4]),
    $dayOfWeekListShort[date('w', $t[5])] . ' ' . date('j', $t[5]),
    $dayOfWeekListShort[date('w', $t[6])] . ' ' . date('j', $t[6]),
    $dayOfWeekListShort[date('w', $t[7])] . ' ' . date('j', $t[7]),
    $dayOfWeekListShort[date('w', $t[8])] . ' ' . date('j', $t[8]),
);


$days = array();
$hour = (int)date('H');
$disabled = [];
for ($i = 0; $i <= 30; $i++) {

    $disabled[$i] = [];

    $timeDays = 60 * 60 * 24 * $i;
    $leftDayOfWeek = date('w', time() + $timeDays);

    // при заказе сегодня до 14, забор возможен сегодня кроме субботы
    if ($i == 0 && ($hour >= 14 && $leftDayOfWeek != 6)) {
        unset($fromNames[$i]);
        unset($fromNamesShort[$i]);
        continue;
    }

    // в воскресенье забор не доступен
    if($leftDayOfWeek == 0){
        unset($fromNames[$i]);
        unset($fromNamesShort[$i]);
    }

    for($j = 0; $j <= 30; $j++){
        // запрещаем для выдачи все дни включая текущий и следующий
        if($j <= $i+1){
            $disabled[$i][] = $j;
            continue;
        }
        $timeDays = 60 * 60 * 24 * $j;
        $rightDayOfWeek = date('w', time() + $timeDays);

        // забор в субботу - возврат в пн запрещен
        if($leftDayOfWeek == 6 && $rightDayOfWeek == 1){
            $disabled[$i][] = $j;
        }

        break;
    }

}

$fromNamesShort2 = $fromNamesShort;

// удаляем первый и второй день для "доставить"
unset($fromNamesShort2[array_keys($fromNamesShort2)[0]]);

// удаляем последний и предпоследний для "забрать"
$keys = array_keys($fromNamesShort);
unset($fromNamesShort[$keys[count($keys)-1]]);
unset($fromNamesShort[$keys[count($keys)-2]]);

/** @noinspection PhpIncludeInspection */
$lang = include( app_path() . '/lang/' . App::getLocale() . '/request.php' );
$result = [];
if($lang && is_array($lang)){
    foreach($lang as $key => $value){
        $result[] = 'TRANS["request.'. $key .'"] = "'. $value .'"';
    }
}

$fromOrderText = trans('request.weGetShort') . ' ';
$toOrderText = trans('request.weReturnShort') . ' ';
$dayOfWeekNow =  date('w');

switch($dayOfWeekNow){

    case 1:
    case 2:
    case 3:
    case 4:
        if($hour >= 18){
            $fromOrderText .= trans('request.завтра') . ', ' . trans('request.before') . ' 11:00';
            $toOrderText .= trans('request.завтра') . ', ' . trans('request.after') . ' 18:00';
        }elseif($hour >= 16){
            $fromOrderText .= trans('request.сегодня') . ', ' . trans('request.before') . ' 22:00';
            $toOrderText .= trans('request.завтра') . ', ' . trans('request.after') . ' 15:00';
        }elseif($hour >= 14){
            $fromOrderText .= trans('request.сегодня') . ', ' . trans('request.before') . ' 19:00';
            $toOrderText .= trans('request.завтра') . ', ' . trans('request.after') . ' 12:00';
        }elseif($hour >= 12){
            $fromOrderText .= trans('request.сегодня') . ', ' . trans('request.before') . ' 17:00';
            $toOrderText .= trans('request.завтра') . ', ' . trans('request.after') . ' 9:00';
        }elseif($hour >= 10){
            $fromOrderText .= trans('request.сегодня') . ', ' . trans('request.before') . ' 15:00';
            $toOrderText .= trans('request.сегодня') . ', ' . trans('request.after') . ' 20:00';
        }elseif($hour >= 8){
            $fromOrderText .= trans('request.сегодня') . ', ' . trans('request.before') . ' 13:00';
            $toOrderText .= trans('request.сегодня') . ', ' . trans('request.after') . ' 18:00';
        }else{
            $fromOrderText .= trans('request.сегодня') . ', ' . trans('request.before') . ' 11:00';
            $toOrderText .= trans('request.сегодня') . ', ' . trans('request.after') . ' 18:00';
        }
        break;
    case 5:
        if($hour >= 18){
            $fromOrderText .= trans('request.в понедельник') . ', ' . trans('request.before') . ' 11:00';
            $toOrderText .= trans('request.в понедельник') . ', ' . trans('request.after') . ' 18:00';
        }elseif($hour >= 16){
            $fromOrderText .= trans('request.сегодня') . ', ' . trans('request.before') . ' 22:00';
            $toOrderText .= trans('request.в понедельник') . ', ' . trans('request.after') . ' 15:00';
        }elseif($hour >= 14){
            $fromOrderText .= trans('request.сегодня') . ', ' . trans('request.before') . ' 19:00';
            $toOrderText .= trans('request.в понедельник') . ', ' . trans('request.after') . ' 12:00';
        }elseif($hour >= 12){
            $fromOrderText .= trans('request.сегодня') . ', ' . trans('request.before') . ' 17:00';
            $toOrderText .= trans('request.в понедельник') . ', ' . trans('request.after') . ' 9:00';
        }elseif($hour >= 10){
            $fromOrderText .= trans('request.сегодня') . ', ' . trans('request.before') . ' 15:00';
            $toOrderText .= trans('request.сегодня') . ', ' . trans('request.after') . ' 20:00';
        }elseif($hour >= 8){
            $fromOrderText .= trans('request.сегодня') . ', ' . trans('request.before') . ' 13:00';
            $toOrderText .= trans('request.сегодня') . ', ' . trans('request.after') . ' 18:00';
        }else{
            $fromOrderText .= trans('request.сегодня') . ', ' . trans('request.before') . ' 11:00';
            $toOrderText .= trans('request.сегодня') . ', ' . trans('request.after') . ' 18:00';
        }
        break;
    case 6:
        $fromOrderText .= trans('request.послезавтра') . ', ' . trans('request.before') . ' 11:00';
        $toOrderText .= trans('request.послезавтра') . ', ' . trans('request.after') . ' 18:00';
        break;
    case 0:
        $fromOrderText .= trans('request.завтра') . ', ' . trans('request.before') . ' 11:00';
        $toOrderText .= trans('request.завтра') . ', ' . trans('request.after') . ' 18:00';
        break;

}

// время когда срочного заказа нет
if(
    ($dayOfWeekNow == 2 && $hour >= 12) || // вторник после 12
    ($dayOfWeekNow == 3 && $hour < 18) || // среда до 18
    ($dayOfWeekNow == 5 && $hour >= 12) || // пятница после 12
    ($dayOfWeekNow == 6) || // суббота весь день
    ($dayOfWeekNow == 0 && $hour < 18) // воскресенье до 18
){
    $fromOrderText = null;
    $toOrderText = null;
}

$fromOrderText = null;
$toOrderText = null;

if($hour > 14){
    $defaultDay = 2;
    $defaultHour = 7;
}else{
    $defaultDay = 1;
    $defaultHour = 17;
}

?>

<script type="text/javascript">
    var TRANS = TRANS || {};
    <?= implode(';', $result) . ';' ?>
    <?= 'var TC_HOUR = ' . $hour . ';' ?>
    <?= 'var TC_NAMES = ' . json_encode($fromNames, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES) . ';' ?>
    <?= 'var TC_DEF_DAY = ' . $defaultDay . ';' ?>
    <?= 'var TC_DEF_HOUR = ' . $defaultHour . ';' ?>
</script>

