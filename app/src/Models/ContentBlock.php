<?php

namespace Dryharder\Models;


/**
 * @property integer $id
 * @property string $code
 * @property string $comment
 * @property string $lang
 *
 * @method static ContentBlock[] all()
 * @method static ContentBlock whereCode($code)
 * @method static ContentBlock exists()
 * @method static ContentBlock findOrFail()
 * @method static ContentBlock find()
 */
class ContentBlock extends \Eloquent {

    protected $table = 'content_blocks';

}