<?php


use Dryharder\Agbis\Api;
use Dryharder\Models\ServiceTitle;
use Illuminate\Console\Command;

class ParsePriceCommand extends Command
{


    protected $name = 'dh:prices';

    protected $description = 'Parse price list from Agbis API';

    public function fire()
    {

        $api = new Api();
        $list = $api->PriceList();
        $listGroup1 = [];
        $listGroup2 = [];
        $listGroup3 = [];


        foreach ($list as &$item) {

            foreach ($item as &$val) {
                $val = trim(urldecode($val));
            }

            if ($item->group_p && !in_array($item->group_p, $listGroup1)) {
                $listGroup1[] = $item->group_p;
            }
            if ($item->group_c && !in_array($item->group_c, $listGroup2)) {
                $listGroup2[] = $item->group_c;
            }
            if ($item->top_parent && !in_array($item->top_parent, $listGroup3)) {
                $listGroup3[] = $item->top_parent;
            }

        }

        foreach ($list as $item) {

            $title = ServiceTitle::whereAgbisId($item->id)->first();
            if (!$title) {
                $title = new ServiceTitle();
            }
            $title->agbis_id = $item->id;
            $title->code = $item->code;
            $title->name = $item->name;

            $lang = !empty($title->lang) ? json_decode($title->lang) : (object)[];
            $lang->ru = $item->name;
            $title->lang = json_encode($lang, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $title->save();

        }

    }

}