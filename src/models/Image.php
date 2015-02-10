<?php namespace Kjamesy\Cms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Image extends Eloquent
{
    protected $table = 'cms_images';

    public static $rules = [
        'title' => 'max:255',
        'caption' => 'max:512',
        'url' => 'required|max:512|URL',
        'order' => 'integer'
    ];

    protected static $cache_minutes = 1440;
    protected static $orderBy = ['order', 'asc'];

    public function gallery(){
        return $this->belongsTo('Kjamesy\Cms\Models\Gallery');
    }

    public function imagetranslations(){
        return $this->hasMany('Kjamesy\Cms\Models\ImageTranslation', 'image_id');
    }


    public static function getImageResource(){
        return static::with('gallery')->orderBy(static::$orderBy[0], static::$orderBy[1])->get();
    }

    public static function getSingleImageResource($id){
        return static::with('gallery')->with('imagetranslations.locale')->find($id);
    }

    public static function destroyImages($ids) {
        if ( count($ids) ) {
            foreach ($ids as $id) {
                $image = static::find($id);
                if ($image) {
                    $image->delete();
                }
            }
        }
    }

}