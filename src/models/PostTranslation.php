<?php namespace Kjamesy\Cms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class PostTranslation extends Eloquent
{
    protected $table = 'cms_post_translations';

    public static $rules = [
        'title' => 'required|max:255',
        'summary' => 'required|max:512',
        'content' => 'required'
    ];

    protected static $cache_minutes = 1440;

    public function user(){
        return $this->belongsTo('User');
    }

    public function post(){
        return $this->belongsTo('Kjamesy\Cms\Models\Post', 'post_id');
    }

    public function cmslocale(){
        return $this->belongsTo('Kjamesy\Cms\Models\CmsLocale', 'locale_id');
    }

    public function posttranslationmeta(){
        return $this->hasMany('Kjamesy\Cms\Models\CmsMeta');
    }

    public static function findATranslation($postId, $localeId) {
        return static::wherePostId($postId)->whereLocaleId($localeId)->with('cmslocale')->with('posttranslationmeta')->first();
    }

    public static function findAllTranslations($postId) {
        return static::wherePostId($postId)->get();
    }

    public static function getPostsBelongingToUser($userId) {
        return static::whereHas('user', function($query) use ($userId) { $query->whereId($userId); })->get();
    }
}