<?php namespace Kjamesy\Cms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Post extends Eloquent
{
    protected $table = 'cms_posts';

    public static $rules = [
        'title' => 'required|max:255',
        'summary' => 'required|max:512',
        'content' => 'required',
        'order' => 'integer',
        'create_date' => 'date'
    ];

    protected static $cache_minutes = 1440;
    protected static $orderBy = ['updated_at', 'desc'];

    public function user(){
        return $this->belongsTo('User');
    }

    public function categories(){
        return $this->belongsToMany('Kjamesy\Cms\Models\Category', 'cms_category_post');
    }

    public function posttranslations(){
        return $this->hasMany('Kjamesy\Cms\Models\PostTranslation');
    }

    public function postmeta(){
        return $this->hasMany('Kjamesy\Cms\Models\Meta');
    }

    public static function getPostResource(){
        return static::with('categories')->with('user')->with('posttranslations.locale')->orderBy(static::$orderBy[0], static::$orderBy[1])->get();
    }

    public static function getSinglePostResource($id){
        return static::with('categories')->with('user')->with('posttranslations.locale')->with('postmeta')->find($id);
    }

    public static function changePostsStatus($ids, $action) {
        if ( count($ids) ) {
            if ($action == 'destroy') {
                foreach ($ids as $id) {
                    $post = static::find($id);
                    if ($post) {
                        $post->delete();
                    }
                }
            }
            else {
                foreach ($ids as $id) {
                    $post = static::find($id);
                    if ($post) {
                        if ($action == 'publish')
                            $post->is_online = 1;
                        elseif ($action == 'draft')
                            $post->is_online = 0;
                        elseif ($action == 'trash') {
                            $post->is_deleted = 1;
                        } elseif ($action == 'restore') {
                            $post->is_deleted = 0;
                        }

                        $post->save();
                    }
                }
            }
        }
    }

    public static function getPostsBelongingToUser($userId) {
        return static::whereHas('user', function($query) use ($userId) { $query->whereId($userId); })->get();
    }
}