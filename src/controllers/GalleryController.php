<?php namespace Kjamesy\Cms\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Kjamesy\Cms\Helpers\Miscellaneous;
use Kjamesy\Cms\Models\Gallery;
use Kjamesy\Cms\Models\Image;
use Kjamesy\Cms\Models\ImageTranslation;
use Kjamesy\Cms\Models\Locale;
use Kjamesy\Cms\Models\User;
use Sentinel\Repositories\User\SentinelUserRepositoryInterface;

class GalleryController extends Controller
{
    public function __construct(SentinelUserRepositoryInterface $userRepository) {
        $this->user = $userRepository->retrieveById(Session::get('userId'));
        $this->isAdmin = User::isAdmin( $this->user );
        $this->logged_in_for = $this->user->last_login->diffForHumans();
        $this->rules = Gallery::$rules;
        $this->imageRules = Image::$rules;
        $this->translationRules = ImageTranslation::$rules;
        $this->activeParent = 'galleries';
    }

    public function index() {
        return View::make("cms::galleries.angular", [
            'user' => $this->user,
            'isAdmin' => $this->isAdmin,
            'logged_in_for' => $this->logged_in_for,
            'activeParent' => $this->activeParent,
            'active' => 'allgalleries'
        ]);
    }

    public function destroy() {
        $id = Input::get('id');
        Gallery::whereId($id)->delete();

        Cache::flush();
        return Response::json(['success' => 'Gallery successfully deleted']);
    }

    public function store_image() {
        $inputs = [];
        foreach( Input::get('image') as $key => $input ) {
            if ( ! is_array($input) ) {
                $inputs[$key] = trim($input);

                if ( $key == 'url' ) {
                    $url = $inputs['url'];

                    $dom = new \DOMDocument();
                    $dom->loadHTML($inputs['url']);
                    $xpath = new \DOMXPath($dom);

                    if ( Str::contains($inputs['url'], 'src=') ) {
                        $url = $xpath->evaluate("string(//img/@src)");
                    }

                    elseif ( Str::contains($inputs['url'], "<p>")) {
                        foreach ( $dom->getElementsByTagName('p') as $key => $node ) {
                            if ( ! $key )
                                $url = $node->nodeValue;
                        }
                    }

                    $inputs['url'] = $url;
                }
            }
        }


        $validation = Miscellaneous::validate($inputs, $this->imageRules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else {

            $image = new Image;
            $image->gallery_id = Input::get('galleryId');
            $image->title = Str::length( $inputs['title'] ) ? $inputs['title'] : NULL;
            $image->caption = Str::length( $inputs['caption'] ) ? $inputs['caption'] : NULL;
            $image->url = $inputs['url'];
            $image->order = (int) $inputs['order'];
            $image->save();

            Cache::flush();

            return Response::json(['success' => 'Image successfully saved', 'image' => $image]);
        }
    }

    public function update_image() {
        $inputs = [];
        foreach( Input::get('image') as $key => $input ) {
            if ( ! is_array($input) ) {
                $inputs[$key] = trim($input);

                if ( $key == 'url' ) {
                    $url = $inputs['url'];

                    $dom = new \DOMDocument();
                    $dom->loadHTML($inputs['url']);
                    $xpath = new \DOMXPath($dom);

                    if ( Str::contains($inputs['url'], 'src=') ) {
                        $url = $xpath->evaluate("string(//img/@src)");
                    }

                    elseif ( Str::contains($inputs['url'], "<p>")) {
                        foreach ( $dom->getElementsByTagName('p') as $key => $node ) {
                            if ( ! $key )
                                $url = $node->nodeValue;
                        }
                    }

                    $inputs['url'] = $url;
                }
            }
        }

        $validation = Miscellaneous::validate($inputs, $this->imageRules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else {

            $image = Image::whereGalleryId(Input::get('galleryId'))->find($inputs['id']);
            $image->title = Str::length( $inputs['title'] ) ? $inputs['title'] : NULL;
            $image->caption = Str::length( $inputs['caption'] ) ? $inputs['caption'] : NULL;
            $image->url = $inputs['url'];
            $image->order = (int) $inputs['order'];
            $image->save();

            Cache::flush();

            return Response::json(['success' => 'Image successfully updated', 'image' => $image]);
        }
    }

    public function destroy_image() {
        $id = Input::get('imageId');
        Image::whereId($id)->delete();

        Cache::flush();
        return Response::json(['success' => 'Image successfully deleted']);
    }


    public function show_image($imageId) {
        $image = Image::getSingleImageResource($imageId);
        $locales = Locale::getLocaleResource();

        return Response::json(compact('image', 'locales'));
    }

    public function process_translation() {
        $inputs = [];
        foreach( Input::get('translation') as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        $validation = Miscellaneous::validate($inputs, $this->translationRules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else {

            if ( array_key_exists('id', $inputs) ) { //We are updating
                $translation = ImageTranslation::findATranslation($inputs['image_id'], $inputs['locale_id']);
            }
            else {
                $translation = new ImageTranslation; //It's new
                $translation->image_id = $inputs['image_id'];
                $translation->locale_id = $inputs['locale_id'];
            }

            $translation->title = Str::length( $inputs['title'] ) ? $inputs['title'] : NULL;
            $translation->caption = Str::length( $inputs['caption'] ) ? $inputs['caption'] : NULL;
            $translation->save();

            Cache::flush();

            return Response::json(['success' => 'Translation successfully processed', 'id' => $translation->id]);
        }

    }

    public function destroy_translation() {
        if ( Input::has('translation') ) {
            if ( array_key_exists('id', Input::get('translation')) ) {
                ImageTranslation::find(Input::get('translation')['id'])->delete();
            }
        }

        Cache::flush();
        return Response::json(['success' => 'Translation successfully deleted']);
    }

}