<?php namespace Kjamesy\Cms\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller as BaseController;
use Kjamesy\Cms\Helpers\Miscellaneous;
use Kjamesy\Cms\Models\Gallery;
use Kjamesy\Utility\Utility;

class GalleryResourceController extends BaseController {
    public function __construct(){
        $this->rules = Gallery::$rules;
    }

    public function index(){
        $galleries = Gallery::getGalleryResource();

        return Response::json(compact('galleries'));
    }


    public function store(){
        $inputs = [];
        foreach( Input::all() as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        $validation = Miscellaneous::validate($inputs, $this->rules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else
        {
            $existingSlugs = Gallery::lists('slug');

            $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['name']));


            $gallery = new Gallery();
            $gallery->name = $inputs['name'];
            $gallery->slug = $slug;
            $gallery->save();

            Cache::flush();

            return Response::json(['success' => 'Gallery successfully saved', 'gallery' => $gallery]);
        }
    }

    public function show($id) {
        $gallery = Gallery::getSingleGalleryResource($id);
        return Response::json(compact('gallery'));
    }


    public function update($id) {
        $inputs = [];
        foreach( Input::all() as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        $validation = Miscellaneous::validate($inputs, $this->rules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else
        {
            $existingSlugs = Gallery::lists('slug');
            $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['name']));

            $gallery = Gallery::find($id);
            $gallery->name = $inputs['name'];
            $gallery->slug = $slug;
            $gallery->save();;

            Cache::flush();

            return Response::json(['success' => 'Gallery successfully updated', 'slug' => $gallery->slug, 'updated_at' => $gallery->updated_at]);
        }
    }


}
