<?php namespace Kjamesy\Cms\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Routing\Controller as BaseController;
use Kjamesy\Cms\Helpers\Miscellaneous;
use Kjamesy\Cms\Models\Category;
use Kjamesy\Cms\Models\Locale;

class CategoryResourceController extends BaseController {
    public function __construct(){
        $this->rules = Category::$rules;
    }

    public function index(){
        $categories = Category::getCategoryResource();
        $locales = Locale::getLocaleResource();

        return Response::json(compact('categories', 'locales'));
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
            $category = new Category;
            $category->name = $inputs['name'];
            $category->save();

            Cache::flush();

            return Response::json(['success' => 'Category successfully saved', 'category' => $category]);
        }
    }

    public function show($id)
    {
        $category = Category::getSingleCategoryResource($id);

        return Response::json(compact('category'));
    }


    public function update($id){
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
            $category = Category::find($id);
            $category->name = $inputs['name'];
            $category->save();

            Cache::flush();

            return Response::json(['success' => 'Category successfully updated', 'updated_at' => $category->updated_at]);
        }
    }


}
