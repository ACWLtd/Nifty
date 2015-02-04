<?php namespace Kjamesy\Cms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class CategoryTranslation extends Eloquent
{
    protected $table = 'cms_category_translations';

    public static $rules = ['name' => 'required|max:255'];

    protected static $cacheMinutes = 1440;

    public function category(){
        return $this->belongsTo('Kjamesy\Cms\Models\Category', 'category_id');
    }

    public function cmslocale(){
        return $this->belongsTo('Kjamesy\Cms\Models\CmsLocale', 'locale_id');
    }

    public static function findATranslation($categoryId, $localeId) {
        return static::whereCategoryId($categoryId)->whereLocaleId($localeId)->with('cmslocale')->first();
    }

}