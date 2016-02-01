<?php


namespace Dryharder\Components;


class Order
{


    public static function statusName($num)
    {
        switch ($num) {
            case 1:
                return trans('main.status New');
            case 3:
                return trans('main.status Progress');
            case 2:
                return trans('main.status Canceled');
            case 4:
                return trans('main.status Ready');
            case 5:
                return trans('main.status Issued');
            case 6:
                return trans('main.status Closed');
            default:
                return '';
        }
    }

    public static function isStatusCurrent($status)
    {
        return in_array($status, [1,2,3,4,5]);
    }

    public static function isStatusHistory($status)
    {
        return in_array($status, [1,2,3,4,5]);
    }

}