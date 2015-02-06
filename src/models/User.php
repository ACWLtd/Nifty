<?php namespace Kjamesy\Cms\Models;

use Cartalyst\Sentry\Facades\Laravel\Sentry;

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
        $isAdmin = false;

        $admin = Sentry::findGroupByName('Administrator');
        if ( $user->inGroup($admin) )
            $isAdmin = true;

        return $isAdmin;
    }

    public static function isContributor( $user )
    {
        $isContributor = false;

        $contributor = Sentry::findGroupByName('Contributor');
        if ( $user->inGroup($contributor) )
            $isContributor = true;

        return $isContributor;
    }

    public static function getUserResource(){
        return static::get();
    }

}