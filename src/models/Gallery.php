<?php namespace Kjamesy\Cms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Gallery extends Eloquent
{
    protected $table = 'cms_galleries';

    public static $rules = ['name' => 'required|max:255|unique:cms_galleries'];

    protected static $cacheMinutes = 1440;

    public function images(){
        return $this->hasMany('Kjamesy\Cms\Models\Image', 'gallery_id');
    }

    public static function getGalleryResource(){
        return static::orderBy('name')->get();
    }

    public static function getSingleGalleryResource($id) {
        return static::with(['images' => function($query) {
            $query->orderBy('order', 'asc');
        }])->find($id);
    }

}