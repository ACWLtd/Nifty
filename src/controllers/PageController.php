<?php namespace Kjamesy\Cms\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Kjamesy\Cms\Helpers\Miscellaneous;
use Kjamesy\Cms\Models\Locale;
use Kjamesy\Cms\Models\Meta;
use Kjamesy\Cms\Models\Page;
use Kjamesy\Cms\Models\PageTranslation;
use Kjamesy\Cms\Models\User;
use Kjamesy\Utility\Utility;
use Sentinel\Repositories\User\SentinelUserRepositoryInterface;

class PageController extends \BaseController
{
    public function __construct(SentinelUserRepositoryInterface $userRepository)
    {
        $this->user = $userRepository->retrieveById(Session::get('userId'));
        $this->isAdmin = User::isAdmin( $this->user );
        $this->logged_in_for = $this->user->last_login->diffForHumans();
        $this->activeParent = 'pages';
        $this->translationRules = PageTranslation::$rules;
        $this->customFieldRules = Meta::$rules;
    }

    public function index()
    {
        return View::make('cms::pages.angular', [
            'user' => $this->user,
            'isAdmin' => $this->isAdmin,
            'logged_in_for' => $this->logged_in_for,
            'activeParent' => $this->activeParent,
            'active' => 'allpages'
        ]);
    }

    public function get_parent_options() {
        return Response::json(['parents' => Page::getParentOptions(null)]);
    }

    public function do_bulk_actions($action) {
        try {
            $pageIds = Input::get('pages');
            Page::changePagesStatus($pageIds, $action);

            return Response::json(['success' => "$action successful"]);
        } catch (Exception $e) {
            return Response::json(['error' => 'An error occurred'], 500);
        }
    }

    public function preview($id) {
        $page = Page::find($id);
        return View::make('cms::pages.preview', ['page' => $page]);
    }

    public function get_locale($localeId) {
        $locale = Locale::getSingleLocaleResource($localeId);

        if ( $locale )
            return Response::json(['locale' => $locale]);
        else
            return Response::json(['error' => 'An error occurred'], 500);
    }

    public function save_translation() {
        $inputs = [];
        foreach( Input::get('translation') as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        $validation = Miscellaneous::validate($inputs, $this->translationRules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else {
            $existingSlugs = PageTranslation::lists('slug');

            if ( Str::length( Str::slug($inputs['slug']) ) ) {
                $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['slug']));
            }

            else {
                $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['title']));
            }

            $page = Page::find(Input::get('pageId'));
            $order = $page ? $page->order : 0;

            $translation = new PageTranslation;
            $translation->user_id = $this->user->id;
            $translation->page_id = $page->id;
            $translation->locale_id = Input::get('localeId');
            $translation->title = $inputs['title'];
            $translation->slug = $slug;
            $translation->summary = $inputs['summary'];
            $translation->content = $inputs['content'];
            $translation->order = $order;
            $translation->is_online = $inputs['is_online'];
            $translation->save();

            Cache::flush();

            return Response::json(['success' => 'Translation successfully saved']);
        }
    }

    public function get_translation($pageId, $localeId) {
        $translation = PageTranslation::findATranslation($pageId, $localeId);
        $metaKeys = Meta::getPageTranslationMetaKeysOptionsList($translation->id);

        if ( $translation )
            return Response::json(['translation' => $translation, 'metaKeys' => $metaKeys]);
        else
            return Response::json(['error' => 'An error occurred'], 500);
    }

    public function update_translation() {
        $inputs = [];
        foreach( Input::get('translation') as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        $validation = Miscellaneous::validate($inputs, $this->translationRules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else
        {
            $id = $inputs['id'];
            $oldTranslation = PageTranslation::find($id);

            $slug = $oldTranslation->slug;
            $existingSlugs = PageTranslation::lists('slug');

            if ( $inputs['slug'] == $oldTranslation->slug ) {
                if ( $oldTranslation->title != $inputs['title'] ) {
                    $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['title']));
                }
            }
            elseif ( $inputs['slug'] != $oldTranslation->slug && Str::length( Str::slug($inputs['slug']) ) ) {
                $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['slug']));
            }

            $oldTranslation->user_id = $this->user->id;
            $oldTranslation->title = $inputs['title'];
            $oldTranslation->slug = $slug;
            $oldTranslation->summary = $inputs['summary'];
            $oldTranslation->content = $inputs['content'];
            $oldTranslation->is_online = $inputs['is_online'];
            $oldTranslation->save();


            /*And now let's deal with the Custom Fields. Delete anything not in the input and modify the stored values of those in the input */
            $cFieldIds = [];

            foreach ( Input::get('customFields') as $cField ) {

                $cFieldIds[] = $cField['id'];

                $meta = Meta::wherePageTranslationId($id)->find($cField['id']);
                if ( $meta ) {
                    $meta->meta_value = $cField['meta_value'];
                    $meta->save();
                }
            }

            if ( count($cFieldIds) )
                Meta::wherePageTranslationId($id)->whereNotIn('id', $cFieldIds)->delete();
            else
                Meta::wherePageTranslationId($id)->delete();

            Cache::flush();
            return Response::json(['success' => 'Translation successfully updated', 'slug' => $slug]);
        }
    }

    public function destroy_translation() {
        PageTranslation::find(Input::get('translationId'))->delete();

        Cache::flush();
        return Response::json(['success' => 'Translation successfully deleted']);
    }


    public function store_custom_field($type) {
        $inputs = [];
        foreach( Input::get('customField') as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        $validation = Miscellaneous::validate($inputs, $this->customFieldRules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else {
            $pageId = Input::get('pageId');

            $existingKeys = Meta::listPageMetaKeys($type, $pageId);
            $metaKey = Utility::makeUniqueSlug($existingKeys, Str::slug($inputs['meta_key']));

            $customField = new Meta;
            if ( $type == 'page' )
                $customField->page_id = $pageId;
            elseif ( $type == 'translation' )
                $customField->page_translation_id = $pageId;
            $customField->meta_key = $metaKey;
            $customField->meta_value = $inputs['meta_value'];
            $customField->save();

            Cache::flush();
            return Response::json(['success' => 'Custom Field successfully saved', 'customField' => $customField]);
        }
    }

    public function update_custom_field($type) {
        $inputs = [];
        foreach( Input::get('customField') as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        $validation = Miscellaneous::validate($inputs, $this->customFieldRules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else {
            $pageId = Input::get('pageId');
            $customField = Meta::find($inputs['id']);

            $existingKeys = Meta::listPageMetaKeys($type, $pageId);
            $metaKey = $customField->meta_key;

            if ( Str::slug($inputs['meta_key']) != $customField->meta_key ) {
                $metaKey = Utility::makeUniqueSlug($existingKeys, Str::slug($inputs['meta_key']));
            }

            $customField->meta_key = $metaKey;
            $customField->meta_value = $inputs['meta_value'];
            $customField->save();

            Cache::flush();
            return Response::json(['success' => 'Custom Field successfully updated', 'metaKey' => $metaKey]);
        }
    }

    public function destroy_custom_field($type) {
        $pageId = Input::get('pageId');
        $customFieldId = Input::get('customField')['id'];

        if ( $customFieldId ) {
            if ( $type == 'page' )
                $meta = Meta::wherePageId($pageId)->whereId($customFieldId);
            elseif ( $type == 'translation' )
                $meta = Meta::wherePageTranslationId($pageId)->whereId($customFieldId);

            if ( $meta ) {
                $meta->delete();
                return Response::json(['success' => 'Custom Field successfully destroyed.']);
            }
        }
    }


}