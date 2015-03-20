<?php namespace Kjamesy\Cms\Models;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Illuminate\Support\Facades\Redirect;

class User extends \Sentinel\Models\User
{

    public static $rules = [
        'first_name' => 'required|max:128',
        'last_name' => 'required|max:128',
        'email' => 'required|email|unique:users',
        'username' => 'max:128|unique:users',
        'password' => 'min:6|confirmed',
        'new_password' => 'min:6|confirmed'
    ];

    public static $newUserRules = [
        'first_name' => 'required|max:128',
        'last_name' => 'required|max:128',
        'email' => 'required|email|unique:users',
        'username' => 'max:128|unique:users',
        'password' => 'required|min:6|confirmed'
    ];

    public static $passwordRules = [ 'new_password' => 'required|min:6|confirmed' ];

    public static function isAdmin( $user )
    {
        if ( $user ) { //Try and fix the trying to get property of a non-object bug
            $isAdmin = false;

            $admin = Sentry::findGroupByName('Administrator');
            if ($user->inGroup($admin))
                $isAdmin = true;

            return $isAdmin;
        }
        else {
            Redirect::route('logout');
        }
    }

    public static function isContributor( $user )
    {
        if ( $user ) { //Try and fix the trying to get property of a non-object bug
            $isContributor = false;

            $contributor = Sentry::findGroupByName('Contributor');
            if ( $user->inGroup($contributor) )
                $isContributor = true;

            return $isContributor;
        }
        else {
            Redirect::route('logout');
        }
    }

    public static function getUserResource(){
        return static::get();
    }

}