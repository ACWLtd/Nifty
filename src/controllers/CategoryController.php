<?php namespace Kjamesy\Cms\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Routing\Controller as BaseController;
use Kjamesy\Cms\Helpers\Miscellaneous;
use Kjamesy\Cms\Models\Category;
use Kjamesy\Cms\Models\CategoryTranslation;
use Kjamesy\Cms\Models\Locale;
use Kjamesy\Cms\Models\User;

class CategoryController extends BaseController
{
    public function __construct() {
        $this->middleware('manage_content');
        $this->user = Auth::user();
        $this->isAdmin = User::isAdmin( $this->user );
        $this->logged_in_for = $this->user->last_login->diffForHumans();
        $this->rules = Category::$rules;
        $this->activeParent = 'posts';
        $this->translationRules = CategoryTranslation::$rules;
    }

    public function index() {
        return View::make("cms::categories.angular", [
            'user' => $this->user,
            'isAdmin' => $this->isAdmin,
            'logged_in_for' => $this->logged_in_for,
            'activeParent' => $this->activeParent,
            'active' => 'categories'
        ]);
    }

    public function destroy() {
        $id = Input::get('id');
        Category::whereId($id)->delete();

        Cache::flush();
        return Response::json(['success' => 'Category successfully deleted']);
    }

    public function get_locale($localeId) {
        $locale = Locale::getSingleLocaleResource($localeId);

        if ( $locale )
            return Response::json(['locale' => $locale]);
        else
            return Response::json(['error' => 'An error occurred'], 500);
    }

    public function save_translation() {
        $name = trim(Input::get('translation')['name']);

        $validation = Miscellaneous::validate(['name' => $name], $this->translationRules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else
        {
            $translation = new CategoryTranslation;
            $translation->category_id = Input::get('categoryId');
            $translation->locale_id = Input::get('localeId');
            $translation->name = $name;
            $translation->save();

            Cache::flush();

            return Response::json(['success' => 'Translation successfully saved']);
        }
    }

    public function get_translation($categoryId, $localeId) {
        $translation = CategoryTranslation::findATranslation($categoryId, $localeId);

        if ( $translation )
            return Response::json(['translation' => $translation]);
        else
            return Response::json(['error' => 'An error occurred'], 500);
    }

    public function update_translation() {
        $name = trim(Input::get('translation')['name']);

        $validation = Miscellaneous::validate(['name' => $name], $this->translationRules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else
        {
            $translation = CategoryTranslation::find(Input::get('translation')['id']);
            $translation->name = $name;
            $translation->save();

            Cache::flush();

            return Response::json(['success' => 'Translation successfully updated']);
        }
    }

    public function destroy_translation() {
        CategoryTranslation::find(Input::get('translationId'))->delete();

        Cache::flush();
        return Response::json(['success' => 'Translation successfully deleted']);
    }

}