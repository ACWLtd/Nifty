<?php namespace Kjamesy\Cms\Controllers;


use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Sentinel\Managers\Session\SentinelSessionManagerInterface;
use Sentinel\Services\Forms\LoginForm;

class AuthController extends \BaseController {

    public function __construct(SentinelSessionManagerInterface $sessionManager, LoginForm $loginForm) {
        $this->session = $sessionManager;
        $this->loginForm = $loginForm;
    }

    public function login() {
        if ( Sentry::check() )
            return Redirect::route('admin');

        return View::make('cms::auth.login');
    }

    public function do_login() {
        $data = Input::all();

        $this->loginForm->validate($data);
        $result = $this->session->store($data);

        if ( $result->isSuccessful() ) {
            return Redirect::intended(URL::route('admin'));
        }

        else {
            Session::flash('error', $result->getMessage());
            return Redirect::route('login')->withInput();
        }
    }

    public function logout() {
        $this->session->destroy();
        return Redirect::route('login');
    }
}
