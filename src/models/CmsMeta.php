<?php namespace Kjamesy\Cms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class CmsMeta extends Eloquent
{
    protected $table = 'cms_pages_posts_meta';

    public static $rules = ['meta_key' => 'required|max:255', 'meta_value' => 'required'];

    protected static $cacheMinutes = 1440;

    public function page(){
        return $this->belongsTo('Kjamesy\Cms\Models\Page', 'page_id');
    }

    public function post(){
        return $this->belongsTo('Kjamesy\Cms\Models\Post', 'post_id');
    }

    public function pagetranslation(){
        return $this->belongsTo('Kjamesy\Cms\Models\PageTranslation', 'page_translation_id');
    }

    public function posttranslation(){
        return $this->belongsTo('Kjamesy\Cms\Models\PostTranslation', 'post_translation_id');
    }

    public static function listPageMetaKeys($type, $pageId) {
        if ( $type == 'page' )
            return static::wherePageId($pageId)->lists('meta_key');
        elseif ( $type == 'translation' )
            return static::wherePageTranslationId($pageId)->lists('meta_key');
    }

    public static function listPostMetaKeys($type, $postId) {
        if ( $type == 'post' )
            return static::wherePostId($postId)->lists('meta_key');
        elseif ( $type == 'translation' )
            return static::wherePostTranslationId($postId)->lists('meta_key');
    }

    public static function getPageMetaKeysOptionsList($pageId) {
        $pageKeys = static::wherePageId($pageId)->lists('meta_key');

        if ( count($pageKeys) ) {
            return static::where(function($query) use($pageId) { $query->where('page_id', '<>', $pageId)->orWhereNull('page_id'); })
                ->whereNotIn('meta_key', $pageKeys)
                ->orderBy('meta_key', 'asc')
                ->groupBy('meta_key')
                ->lists('meta_key');
        }
        else {
            return static::where('page_id', '<>', $pageId)
                ->orWhereNull('page_id')
                ->orderBy('meta_key', 'asc')
                ->groupBy('meta_key')
                ->lists('meta_key');
        }

    }

    public static function getPageTranslationMetaKeysOptionsList($translationId) {
        $translationKeys = static::wherePageTranslationId($translationId)->lists('meta_key');

        if ( count($translationKeys) ) {
            return static::where(function($query) use($translationId) { $query->where('page_translation_id', '<>', $translationId)->orWhereNull('page_translation_id'); })
                ->whereNotIn('meta_key', $translationKeys)
                ->orderBy('meta_key', 'asc')
                ->groupBy('meta_key')
                ->lists('meta_key');
        }
        else {
            return static::where('page_translation_id', '<>', $translationId)
                ->orWhereNull('page_translation_id')
                ->orderBy('meta_key', 'asc')
                ->groupBy('meta_key')
                ->lists('meta_key');
        }
    }

    public static function getPostMetaKeysOptionsList($postId) {
        $postKeys = static::wherePostId($postId)->lists('meta_key');

        if ( count($postKeys) ) {
            return static::where(function($query) use($postId) { $query->where('post_id', '<>', $postId)->orWhereNull('post_id'); })
                ->whereNotIn('meta_key', $postKeys)
                ->orderBy('meta_key', 'asc')
                ->groupBy('meta_key')
                ->lists('meta_key');
        }
        else {
            return static::where('post_id', '<>', $postId)
                ->orWhereNull('post_id')
                ->orderBy('meta_key', 'asc')
                ->groupBy('meta_key')
                ->lists('meta_key');
        }

    }

    public static function getPostTranslationMetaKeysOptionsList($translationId) {
        $translationKeys = static::wherePostTranslationId($translationId)->lists('meta_key');

        if ( count($translationKeys) ) {
            return static::where(function($query) use($translationId) { $query->where('post_translation_id', '<>', $translationId)->orWhereNull('post_translation_id'); })
                ->whereNotIn('meta_key', $translationKeys)
                ->orderBy('meta_key', 'asc')
                ->groupBy('meta_key')
                ->lists('meta_key');
        }
        else {
            return static::where('post_translation_id', '<>', $translationId)
                ->orWhereNull('post_translation_id')
                ->orderBy('meta_key', 'asc')
                ->groupBy('meta_key')
                ->lists('meta_key');
        }
    }

}