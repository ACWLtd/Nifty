<?php namespace Kjamesy\Cms\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller as BaseController;
use Kjamesy\Cms\Helpers\Miscellaneous;
use Kjamesy\Cms\Models\Category;
use Kjamesy\Cms\Models\Locale;
use Kjamesy\Cms\Models\Meta;
use Kjamesy\Cms\Models\Post;
use Kjamesy\Cms\Models\PostTranslation;
use Kjamesy\Utility\Utility;

class PostResourceController extends BaseController {
    public function __construct()
    {
        $this->user = Auth::user();
        $this->rules = Post::$rules;
    }

    public function index()
    {
        $posts = Post::getPostResource();
        $locales = Locale::getLocaleResource();
        return Response::json(compact('posts', 'locales'));
    }


    public function store()
    {
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
            $existingSlugs = Post::lists('slug');

            if ( Str::length( Str::slug($inputs['slug']) ) ) {
                $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['slug']));
            }

            else {
                $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['title']));
            }

            $order = $inputs['order'];
            $created_at = Input::has('create_date') ? Carbon::createFromFormat('Y-m-d', $inputs['create_date'])->toDateTimeString() : Carbon::now()->toDateTimeString();

            if ( strlen($order) == 0 )
                $order = 0;

            $categories = Input::get('categories') == null ? [Category::first()->id] : Input::get('categories');

            $post = new Post;
            $post->user_id = $this->user->id;
            $post->title = $inputs['title'];
            $post->slug = $slug;
            $post->summary = $inputs['summary'];
            $post->content = $inputs['content'];
            $post->order = $order;
            $post->is_online = $inputs['is_online'];
            $post->created_at = $created_at;
            $post->save();

            $post->categories()->sync($categories);

            Cache::flush();

            return Response::json(['success' => 'Post successfully saved', 'id' => $post->id]);
        }
    }

    public function show($id)
    {
        $post = Post::getSinglePostResource($id);
        $categories = Category::getCategoryList();
        $locales = Locale::getLocaleResource();
        $metaKeys = Meta::getPostMetaKeysOptionsList($post->id);

        return Response::json(compact('post', 'categories', 'locales', 'metaKeys'));
    }


    public function update($id)
    {
        $inputs = [];
        foreach( Input::get('post') as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        $validation = Miscellaneous::validate($inputs, $this->rules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else
        {
            $post = Post::find($id);
            $slug = $post->slug;
            $existingSlugs = Post::lists('slug');

            if ( $inputs['slug'] == $post->slug ) {
                if ( $post->title != $inputs['title'] ) {
                    $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['title']));
                }
            }
            elseif ( $inputs['slug'] != $post->slug && Str::length($inputs['slug']) ) {
                $slug = Utility::makeUniqueSlug($existingSlugs, Str::slug($inputs['slug']));
            }

            $order = $inputs['order'];

            if ( strlen($order) == 0 )
                $order = 0;

            //Update All Translations Order If The Post Order Has Changed
            if ( $post->order != $order ) {
                $translations = PostTranslation::findAllTranslations($post->id);
                foreach( $translations as $translation) {
                    $translation->order = $order;
                    $translation->save();
                }
            }

            $created_at = ( array_key_exists('create_date', $inputs) && strlen($inputs['create_date']) )
                ? Carbon::createFromFormat('Y-m-d', $inputs['create_date'])->toDateTimeString()
                : $post->created_at;
            $categories = Input::get('post')['categories'] == null ? [Category::first()->id] : Input::get('post')['categories'];

            $post->user_id = $this->user->id;
            $post->title = $inputs['title'];
            $post->slug = $slug;
            $post->summary = $inputs['summary'];
            $post->content = $inputs['content'];
            $post->order = $order;
            $post->is_online = $inputs['is_online'];
            $post->created_at = $created_at;
            $post->save();

            $post->categories()->sync($categories);

            /*And now let's deal with the Custom Fields. Delete anything not in the input and modify the stored values of those in the input */
            $cFieldIds = [];

            foreach ( Input::get('customFields') as $cField ) {

                $cFieldIds[] = $cField['id'];

                $meta = Meta::wherePostId($id)->find($cField['id']);
                if ( $meta ) {
                    $meta->meta_value = $cField['meta_value'];
                    $meta->save();
                }
            }

            if ( count($cFieldIds) )
                Meta::wherePostId($id)->whereNotIn('id', $cFieldIds)->delete();
            else
                Meta::wherePostId($id)->delete();

            Cache::flush();

            return Response::json(['success' => 'Post successfully updated', 'slug' => $slug]);
        }
    }

}
