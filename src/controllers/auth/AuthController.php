<?php

namespace Kjamesy\Cms\Controllers\Auth;

use Kjamesy\Cms\Models\User;
use Validator;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
        $this->redirectPath = route('home');
        $this->username = 'username';
        $this->defaultRole = [3];
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'username' => 'required|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Show the registration form
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return view('cms::auth.register');
    }

    /**
     * Handle a registration request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

//        Auth::login($this->create($request->all()));
        $user = $this->create($request->all());
        $user->roles()->sync($this->defaultRole);

        return redirect(route('auth.register'))->withSuccess('You are now registered. Please contact admin to activate your registration and login.');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            'active' => 0,
        ]);
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        return view('cms::auth.login');
    }

    /**
     * Override the method inside the trait to allow users to login using username/email
     * @param Request $request
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        $field = $this->loginUsername();
        $usernameInput = $request->$field;

        $column = filter_var($usernameInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username'; //If it looks like an email, use email column otherwise, use username

        return [$column => $usernameInput, 'password' => $request->password, 'active' => 1];
    }

    /**
     * Handle authenticated user request
     * @param Request $request
     * @param $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        $user->is_logged_in = 1;
        $user->last_login = Carbon::now();
        $user->save();

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        $user = Auth::user();
        $user->is_logged_in = 0;
        $user->save();

        Auth::logout();

        return redirect(route('home'));
    }
}
