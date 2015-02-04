<?php namespace Kjamesy\Cms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Category extends Eloquent
{
    protected $table = 'cms_categories';

    public static $rules = ['name' => 'required|max:255|unique:cms_categories'];

    protected static $cacheMinutes = 1440;

    public function posts(){
        return $this->belongsToMany('Kjamesy\Cms\Models\Post', 'cms_category_post');
    }

    public function categorytranslations(){
        return $this->hasMany('Kjamesy\Cms\Models\CategoryTranslation', 'category_id');
    }

    public function postsCount(){
        return $this->belongsToMany('Kjamesy\Cms\Models\Post', 'cms_category_post')
            ->selectRaw('count(cms_posts.id) as count')
            ->groupBy('cms_category_post.category_id');
    }

// accessor
    public function getPostsCountAttribute(){
        if ( ! array_key_exists('postsCount', $this->relations)) $this->load('postsCount');

        $related = $this->getRelation('postsCount')->first();

        return ($related) ? $related->aggregate : 0;
    }

    public static function getCategoryList(){
        return static::whereNotIn('id', [1])->lists('name', 'id');
    }

    public static function getCategoryResource(){
        return static::with('postsCount')->with('categorytranslations.cmslocale')->orderBy('name')->get();
    }

    public static function getSingleCategoryResource($id) {
        return static::find($id);
    }

}