<?php namespace Kjamesy\Cms\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Kjamesy\Cms\Helpers\Miscellaneous;
use Kjamesy\Cms\Models\Category;
use Kjamesy\Cms\Models\Locale;
use Kjamesy\Cms\Models\Meta;
use Kjamesy\Cms\Models\Post;
use Kjamesy\Cms\Models\PostTranslation;
use Kjamesy\Cms\Models\User;
use Kjamesy\Utility\Utility;
use Sentinel\Repositories\User\SentinelUserRepositoryInterface;

class PostController extends Controller
{
    public function __construct(SentinelUserRepositoryInterface $userRepository){
        $this->user = $userRepository->retrieveById(Session::get('userId'));
        $this->isAdmin = User::isAdmin( $this->user );
        $this->logged_in_for = $this->user->last_login->diffForHumans();
        $this->translationRules = PostTranslation::$rules;
        $this->activeParent = 'posts';
        $this->customFieldRules = Meta::$rules;
    }

    public function index(){
        return View::make('cms::posts.angular', [
            'user' => $this->user,
            'isAdmin' => $this->isAdmin,
            'logged_in_for' => $this->logged_in_for,
            'activeParent' => $this->activeParent,
            'active' => 'allposts'
        ]);
    }


    public function get_category_options() {
        return Response::json(['categories' => Category::getCategoryList()]);
    }

    public function do_bulk_actions($action) {
        try {
            $postIds = Input::get('posts');
            Post::changePostsStatus($postIds, $action);

            Cache::flush();

            return Response::json(['success' => "$action successful"]);
        } catch (Exception $e) {
            return Response::json(['error' => 'An error occurred'], 500);
        }
    }

    public function preview($id) {
        $post = Post::find($id);
        return View::make('cms::posts.preview', ['post' => $post]);
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
        else
        {
            $existingSlugs = PostTranslation::lists('slug');

            if ( Str::length( Str::slug($inputs['slug']) ) ) {
                $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['slug']));
            }

            else {
                $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['title']));
            }

            $post = Post::find(Input::get('postId'));
            $order = $post ? $post->order : 0;
            $created_at = ( array_key_exists('create_date', $inputs) && strlen($inputs['create_date']) )
                ? Carbon::createFromFormat('Y-m-d', $inputs['create_date'])->toDateTimeString()
                : Carbon::now()->toDateTimeString();

            $translation = new PostTranslation;
            $translation->user_id = $this->user->id;
            $translation->post_id = $post->id;
            $translation->locale_id = Input::get('localeId');
            $translation->title = $inputs['title'];
            $translation->slug = $slug;
            $translation->summary = $inputs['summary'];
            $translation->content = $inputs['content'];
            $translation->order = $order;
            $translation->is_online = $inputs['is_online'];
            $translation->created_at = $created_at;
            $translation->save();

            Cache::flush();

            return Response::json(['success' => 'Translation successfully saved']);
        }
    }

    public function get_translation($postId, $localeId) {
        $translation = PostTranslation::findATranslation($postId, $localeId);
        $metaKeys = Meta::getPostTranslationMetaKeysOptionsList($translation->id);

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
            $oldTranslation = PostTranslation::find($id);

            $slug = $oldTranslation->slug;
            $existingSlugs = PostTranslation::lists('slug');

            if ( $inputs['slug'] == $oldTranslation->slug ) {
                if ( $oldTranslation->title != $inputs['title'] ) {
                    $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['title']));
                }
            }
            elseif ( $inputs['slug'] != $oldTranslation->slug && Str::length( Str::slug($inputs['slug']) ) ) {
                $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['slug']));
            }

            $created_at = ( array_key_exists('create_date', $inputs) && strlen($inputs['create_date']) )
                ? Carbon::createFromFormat('Y-m-d', $inputs['create_date'])->toDateTimeString()
                : $oldTranslation->created_at;

            $oldTranslation->user_id = $this->user->id;
            $oldTranslation->title = $inputs['title'];
            $oldTranslation->slug = $slug;
            $oldTranslation->summary = $inputs['summary'];
            $oldTranslation->content = $inputs['content'];
            $oldTranslation->is_online = $inputs['is_online'];
            $oldTranslation->created_at = $created_at;
            $oldTranslation->save();


            /*And now let's deal with the Custom Fields. Delete anything not in the input and modify the stored values of those in the input */
            $cFieldIds = [];

            foreach ( Input::get('customFields') as $cField ) {

                $cFieldIds[] = $cField['id'];

                $meta = Meta::wherePostTranslationId($id)->find($cField['id']);
                if ( $meta ) {
                    $meta->meta_value = $cField['meta_value'];
                    $meta->save();
                }
            }

            if ( count($cFieldIds) )
                Meta::wherePostTranslationId($id)->whereNotIn('id', $cFieldIds)->delete();
            else
                Meta::wherePostTranslationId($id)->delete();

            Cache::flush();
            return Response::json(['success' => 'Translation successfully updated', 'slug' => $slug]);
        }
    }

    public function destroy_translation() {
        PostTranslation::find(Input::get('translationId'))->delete();

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
            $postId = Input::get('postId');

            $existingKeys = Meta::listPostMetaKeys($type, $postId);
            $metaKey = Utility::makeUniqueSlug($existingKeys, Str::slug($inputs['meta_key']));

            $customField = new Meta;
            if ( $type == 'post' )
                $customField->post_id = $postId;
            elseif ( $type == 'translation' )
                $customField->post_translation_id = $postId;
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
            $postId = Input::get('postId');
            $customField = Meta::find($inputs['id']);

            $existingKeys = Meta::listPostMetaKeys($type, $postId);
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
        $postId = Input::get('postId');
        $customFieldId = Input::get('customField')['id'];

        if ( $customFieldId ) {
            if ( $type == 'post' )
                $meta = Meta::wherePostId($postId)->whereId($customFieldId);
            elseif ( $type == 'translation' )
                $meta = Meta::wherePostTranslationId($postId)->whereId($customFieldId);

            if ( $meta ) {
                $meta->delete();

                Cache::flush();
                return Response::json(['success' => 'Custom Field successfully destroyed.']);
            }
        }
    }
}