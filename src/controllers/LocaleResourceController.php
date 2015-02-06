<?php namespace Kjamesy\Cms\Controllers;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Kjamesy\Cms\Helpers\Miscellaneous;
use Kjamesy\Cms\Models\Locale;

class LocaleResourceController extends \BaseController {
    public function __construct(){
        $this->rules = Locale::$rules;
    }

    public function index(){
        $locales = Locale::getLocaleResource();

        return Response::json(compact('locales', 'locales'));
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
            $locale = new Locale;
            $locale->locale = $inputs['locale'];
            $locale->save();

            Cache::flush();

            return Response::json(['success' => 'Locale successfully saved', 'locale' => $locale]);
        }
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
            $locale = Locale::find($id);
            $locale->locale = $inputs['locale'];
            $locale->save();

            Cache::flush();

            return Response::json(['success' => 'Locale successfully updated', 'updated_at' => $locale->updated_at]);
        }
    }


}
