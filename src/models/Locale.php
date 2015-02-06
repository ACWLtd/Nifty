<?php namespace Kjamesy\Cms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Locale extends Eloquent
{
    protected $table = 'cms_locales';

    public static $rules = ['locale' => 'required|max:255|unique:cms_locales'];

    protected static $cacheMinutes = 1440;

    public function pagetranslations(){
        return $this->hasMany('Kjamesy\Cms\Models\PageTranslation', 'locale_id');
    }

    public function categorytranslations(){
        return $this->hasMany('Kjamesy\Cms\Models\CategoryTranslation', 'locale_id');
    }

    public function posttranslations(){
        return $this->hasMany('Kjamesy\Cms\Models\PostTranslation', 'locale_id');
    }

    public static function getLocaleResource() {
        return static::get();
    }

    public static function getSingleLocaleResource($id) {
        return static::find($id);
    }

}