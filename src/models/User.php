<?php

namespace Kjamesy\Cms\Models;

class User extends \App\User
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'username', 'password', 'active'];

    /**
     * Dates to be converted into carbon objects automatically
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'last_login'];

    /**
     * Validation rules for updating user record
     * @var array
     */
    public static $rules = [
        'first_name' => 'required|max:128',
        'last_name' => 'required|max:128',
        'email' => 'required|email|unique:users',
        'username' => 'max:128|unique:users',
        'password' => 'min:6|confirmed',
        'new_password' => 'min:6|confirmed'
    ];

    /**
     * Validation rules for creating new user record
     * @var array
     */
    public static $newUserRules = [
        'first_name' => 'required|max:128',
        'last_name' => 'required|max:128',
        'email' => 'required|email|unique:users',
        'username' => 'max:128|unique:users',
        'password' => 'required|min:6|confirmed'
    ];

    /**
     * Validation rules for changing password
     * @var array
     */
    public static $passwordRules = [ 'new_password' => 'required|min:6|confirmed' ];

    /**
     * Relationship between User and Role
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->belongsToMany('Kjamesy\Cms\Models\Role');
    }

    /**
     * Determine if User's roles contain the supplied role
     * @param $roles
     * @param $roleName
     * @return bool
     */
    public static function hasRole($roles, $roleName)
    {
        $result = false;

        foreach( $roles as $role ) {
            if ( $role->name == $roleName ) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * A One to Many relationship between User and Page
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pages()
    {
        return $this->hasMany('Kjamesy\Cms\Models\Page');
    }

    /**
     * A One to Many relationship between User and Post
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany('Kjamesy\Cms\Models\Post');
    }

    /**
     * A One to Many relationship between User and PageTranslation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pagetranslations()
    {
        return $this->hasMany('Kjamesy\Cms\Models\PageTranslation');
    }

    /**
     * A One to Many relationship between User and PostTranslation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posttranslations()
    {
        return $this->hasMany('Kjamesy\Cms\Models\PostTranslation');
    }

    /**
     * Determine if the supplied user is an admin
     * @param $user
     * @return bool
     */
    public static function isAdmin( $user )
    {
        return static::hasRole($user->roles, 'Administrator');
    }

    /**
     * Determine if the supplied user is a Contributor
     * @param $user
     * @return bool
     */
    public static function isContributor( $user )
    {
        return static::hasRole($user->roles, 'Editor');
    }

    public static function canManageContent( $user )
    {
        return ( static::isAdmin($user) || static::isContributor($user) ) ? true : false;
    }

    /**
     * Get the user resource except the logged in user
     * @param null $exceptId
     * @return mixed
     */
    public static function getUserResource($exceptId = null)
    {
        return $exceptId ? static::whereNotIn('id', $exceptId)->get() : static::get();
    }

}