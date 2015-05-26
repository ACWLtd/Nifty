<?php namespace Kjamesy\Cms\Controllers;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Routing\Controller as BaseController;
use Sentinel\FormRequests\LoginRequest;
use Sentinel\Repositories\Session\SentinelSessionRepositoryInterface;


class AuthController extends BaseController {

    public function __construct(SentinelSessionRepositoryInterface $sessionManager) {
        $this->session = $sessionManager;
    }

    public function login() {
        if ( Sentry::check() )
            return Redirect::route('admin');

        return View::make('cms::auth.login');
    }

    public function do_login(LoginRequest $request) {
        $data = Input::all();

        // Attempt the login
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
