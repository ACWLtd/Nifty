<?php namespace Kjamesy\Cms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class PageTranslation extends Eloquent
{

    protected $table = 'cms_page_translations';

    public static $rules = [
        'title' => 'required|max:255',
        'summary' => 'required|max:512',
        'content' => 'required'
    ];

    protected static $cache_minutes = 1440;
    protected static $orderBy = 'lft';

    public function user(){
        return $this->belongsTo('User');
    }

    public function page(){
        return $this->belongsTo('Kjamesy\Cms\Models\Page', 'page_id');
    }

    public function cmslocale(){
        return $this->belongsTo('Kjamesy\Cms\Models\CmsLocale', 'locale_id');
    }

    public function pagetranslationmeta(){
        return $this->hasMany('Kjamesy\Cms\Models\CmsMeta');
    }

    public static function findATranslation($pageId, $localeId) {
        return static::wherePageId($pageId)->whereLocaleId($localeId)->with('cmslocale')->with('pagetranslationmeta')->first();
    }

    public static function findAllTranslations($pageId) {
        return static::wherePageId($pageId)->get();
    }

    public static function getPagesBelongingToUser($userId) {
        return static::whereHas('user', function($query) use ($userId) { $query->whereId($userId); })->get();
    }
}