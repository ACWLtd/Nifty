<?php namespace Kjamesy\Cms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class PostTranslation extends Eloquent
{
    protected $table = 'cms_post_translations';

    public static $rules = [
        'title' => 'required|max:255',
        'summary' => 'required|max:512',
        'content' => 'required',
        'create_date' => 'date'
    ];

    protected static $cache_minutes = 1440;

    public function user(){
        return $this->belongsTo('User');
    }

    public function post(){
        return $this->belongsTo('Kjamesy\Cms\Models\Post', 'post_id');
    }

    public function locale(){
        return $this->belongsTo('Kjamesy\Cms\Models\Locale', 'locale_id');
    }

    public function posttranslationmeta(){
        return $this->hasMany('Kjamesy\Cms\Models\Meta');
    }

    public static function findATranslation($postId, $localeId) {
        return static::wherePostId($postId)->whereLocaleId($localeId)->with('locale')->with('posttranslationmeta')->first();
    }

    public static function findAllTranslations($postId) {
        return static::wherePostId($postId)->get();
    }

    public static function getPostsBelongingToUser($userId) {
        return static::whereHas('user', function($query) use ($userId) { $query->whereId($userId); })->get();
    }
}