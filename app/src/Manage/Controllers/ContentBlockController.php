<?php

namespace Dryharder\Manage\Controllers;

use App;
use Dryharder\Models\ContentBlock;
use Illuminate\Support\Facades\Response;
use Input;

class ContentBlockController extends BaseController
{

    public function index()
    {
        if (!$this->isAcceptedJson()) {
            return \View::make('man::contentBlocks.index');
        }

        $blocks = ContentBlock::all();
        $result = [];
        foreach ($blocks as $block) {
            $result[] = [
                'id'      => $block->id,
                'code'    => $block->code,
                'comment' => $block->comment,
                'lang'    => json_decode($block->lang),
            ];
        }

        return Response::json($result);

    }


    public function store(){

        $code = Input::get('code');
        $code = trim($code);

        if(preg_match('/[^A-Za-z0-9_-]+/', $code)){
            App::abort(500, 'Недопустимые символы');
        }

        $exists = ContentBlock::whereCode($code)->exists();
        if($exists){
            App::abort(500, 'Код уже существует');
        }

        $block = new ContentBlock();
        $block->code = $code;
        $block->save();

    }

    public function update($id){

        $text = Input::get('content');
        $lang = Input::get('lang');
        $comment = Input::get('comment');
        $change = Input::get('change');

        $block = ContentBlock::findOrFail($id);
        $langContent = $block->lang;
        $langContent = $langContent ? json_decode($langContent): (object)[];
        $langContent->$lang = $text;

        if($change && $change != $block->code) {
            $changeExists = ContentBlock::whereCode($change)
                ->where('id', '!=', $block->id)
                ->exists();
            if ($changeExists) {
                App::abort(500, 'Новый код принадлежит другому блоку');
            }
            $block->code = $change;
        }

        $block->lang = json_encode($langContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $block->comment = $comment;
        $block->save();

    }

    public function delete($id){

        $block = ContentBlock::findOrFail($id);
        $block->delete();

    }


}