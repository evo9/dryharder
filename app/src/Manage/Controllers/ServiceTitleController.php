<?php

namespace Dryharder\Manage\Controllers;

use Dryharder\Models\ServiceTitle;
use Input;
use Response;

class ServiceTitleController extends BaseController
{


    public function index()
    {
        if (!$this->isAcceptedJson()) {
            return \View::make('man::serviceTitle.index');
        }

        $titles = ServiceTitle::all();
        $result = [];
        foreach ($titles as $title) {
            $result[] = [
                'id'   => $title->id,
                'code' => $title->code,
                'name' => $title->name,
                'lang' => json_decode($title->lang),
            ];
        }

        return Response::json($result);

    }

    public function update($id)
    {

        $text = Input::get('content');
        $lang = Input::get('lang');
        $title = ServiceTitle::findOrFail($id);
        $langContent = $title->lang;
        $langContent = $langContent ? json_decode($langContent) : (object)[];
        $langContent->$lang = $text;

        $title->lang = json_encode($langContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $title->save();

    }

}