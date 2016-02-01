<?php

namespace Dryharder\Models;


/**
 * @property integer $id
 * @property integer $agbis_id
 * @property string $code
 * @property string $name
 * @property string $lang
 *
 * @method static ServiceTitle first()
 * @method static ServiceTitle findOrFail()
 * @method static ServiceTitle whereAgbisId($agbis_id)
 * @method static ServiceTitle whereName($name)
 *
 */
class ServiceTitle extends \Eloquent {

    protected $table = 'service_titles';

    /**
     * языковая версия названия услуги
     *
     * @param $lang
     *
     * @return string
     */
    public function lang($lang)
    {
        $texts = json_decode($this->lang);

        return isset($texts->$lang) ? $texts->$lang : $this->name;
    }

}