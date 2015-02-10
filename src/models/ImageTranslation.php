<?php namespace Kjamesy\Cms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class ImageTranslation extends Eloquent
{
    protected $table = 'cms_image_translations';

    public static $rules = [
        'title' => 'max:255',
        'caption' => 'max:512'
    ];

    protected static $cache_minutes = 1440;

    public function image(){
        return $this->belongsTo('Kjamesy\Cms\Models\Image', 'image_id');
    }

    public function locale(){
        return $this->belongsTo('Kjamesy\Cms\Models\Locale', 'locale_id');
    }

    public static function findATranslation($imageId, $localeId) {
        return static::whereImageId($imageId)->whereLocaleId($localeId)->first();
    }

    public static function findAllTranslations($imageId) {
        return static::whereImageId($imageId)->get();
    }

}