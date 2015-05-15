<?php namespace Kjamesy\Cms\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Kjamesy\Cms\Helpers\Miscellaneous;
use Kjamesy\Cms\Models\Page;
use Kjamesy\Cms\Models\PageTranslation;
use Kjamesy\Cms\Models\Post;
use Kjamesy\Cms\Models\PostTranslation;
use Kjamesy\Cms\Models\User;
use Sentinel\Repositories\User\SentinelUserRepositoryInterface;


class UserController extends Controller {

    public function __construct(SentinelUserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
        $this->user = $this->userRepository->retrieveById(Session::get('userId'));
        $this->isAdmin = User::isAdmin( $this->user );
        $this->logged_in_for = $this->user->last_login->diffForHumans();
        $this->rules = User::$rules;
        $this->passwordRules = User::$passwordRules;
        $this->activeParent = 'users';
    }

    public function index(){
        return View::make('cms::users.angular', [
            'user' => $this->user,
            'isAdmin' => $this->isAdmin,
            'logged_in_for' => $this->logged_in_for,
            'activeParent' => $this->activeParent,
            'active' => 'allusers'
        ]);

    }

    public function get_profile(){
        return View::make('cms::users.profile', [
            'user' => $this->user,
            'isAdmin' => $this->isAdmin,
            'logged_in_for' => $this->logged_in_for,
            'activeParent' => $this->activeParent,
            'active' => 'profile',
            'roles' => $this->user->getGroups(),
            'userSince' => $this->user->created_at->diffForHumans(),
            'userCreatedAt' => $this->user->created_at->format('D jS \\of M, Y H:i'),
            'loggedInAt' => $this->user->last_login->format('D jS \\of M, Y H:i')
        ]);
    }

    public function update_profile(){
        $inputs = [];

        foreach( Input::all() as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        if ( $inputs['email'] == $this->user->email )
            unset($this->rules['email']);
        if ( Input::has('username') && $inputs['username'] == $this->user->username )
            unset($this->rules['username']);

        $validation = Miscellaneous::validate($inputs, $this->rules);

        if ( $validation !== true )
            return Redirect::back()->withErrors($validation)->withValidationerror('')->withInput();
        else {
            $this->user->first_name = $inputs['first_name'];
            $this->user->last_name = $inputs['last_name'];
            $this->user->email = $inputs['email'];
            $this->user->username = Input::has('username') ? $inputs['username'] : NULL;
            $this->user->save();

            Cache::flush();
            return Redirect::back()->withSuccess('Profile updated.');
        }
    }

    public function get_profile_password(){
        return View::make('cms::users.profile-password', [
            'user' => $this->user,
            'isAdmin' => $this->isAdmin,
            'logged_in_for' => $this->logged_in_for,
            'activeParent' => $this->activeParent,
            'active' => 'profile'
        ]);
    }

    public function update_profile_password(){
        if ( ! Hash::check( Input::get('existing_password'), $this->user->password ) )
            return Redirect::back()->withExistingPassError('Wrong password.')->withValidationerror('');

        $validation = Miscellaneous::validate(Input::all(), $this->passwordRules);

        if( $validation !== true )
            return Redirect::back()->withErrors($validation)->withValidationerror('')->withInput();

        else {
            $this->user->password = Input::get('new_password');
            $this->user->save();

            return Redirect::back()->withSuccess('Password updated.');
        }
    }

    public function do_action($action) {
        $id = Input::get('user')['id'];
        $suffix = '';

        switch ($action) {
            case 'suspend':
                $this->userRepository->suspend($id);
                $suffix = 'suspended for 15 minutes';
                break;
            case 'unSuspend':
                $this->userRepository->unsuspend($id);
                $suffix = 'unsuspended';
                break;
            case 'ban':
                $this->userRepository->ban($id);
                $suffix = 'banned';
                break;
            case 'unBan':
                $this->userRepository->unban($id);
                $suffix = 'unbanned';
                break;
            case 'destroy':
                $pages = Page::getPagesBelongingToUser($id);
                if ( count($pages) ) {
                    foreach ($pages as $page ) {
                        $page->user_id = $this->user->id;
                        $page->save();
                    }
                }

                $pageTranslations = PageTranslation::getPagesBelongingToUser($id);
                if ( count($pageTranslations) ) {
                    foreach ($pageTranslations as $page ) {
                        $page->user_id = $this->user->id;
                        $page->save();
                    }
                }

                $posts = Post::getPostsBelongingToUser($id);
                if ( count($posts) ) {
                    foreach ($posts as $post ) {
                        $post->user_id = $this->user->id;
                        $post->save();
                    }
                }

                $postTranslations = PostTranslation::getPostsBelongingToUser($id);
                if ( count($postTranslations) ) {
                    foreach ($postTranslations as $post ) {
                        $post->user_id = $this->user->id;
                        $post->save();
                    }
                }

                $this->userRepository->destroy($id);
                $suffix = 'destroyed. All pages/posts belonging to the destroyed user have been assigned to the currently logged in user';
                break;
        }

        Cache::flush();
        return Response::json(["success" => "User successfully $suffix", 'user' => $this->userRepository->retrieveById($id)]);
    }

}
