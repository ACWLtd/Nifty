<?php namespace Kjamesy\Cms\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Kjamesy\Cms\Helpers\Miscellaneous;
use Kjamesy\Cms\Models\Role;
use Kjamesy\Cms\Models\User;

class UserResourceController extends UserController {
    public function __construct(){
        $this->user = Auth::user();
        $this->rules = User::$rules;
        $this->newUserRules = User::$newUserRules;
        $this->defaultRole = [3];
    }

    public function index(){
        $user = $this->user;
        $users = User::getUserResource([$exceptId = $user->id]);
        $groups = Role::getRoleResource();

        foreach($users as $aUser){
            $aUser->status = $aUser->active ? 'Active' : 'Inactive';
            $aUser->groups = $aUser->roles;
        }

        return Response::json(compact('users', 'user', 'groups'));
    }

    public function store(){
        $inputs = [];
        foreach( Input::all() as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        $validation = Miscellaneous::validate($inputs, $this->newUserRules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else {
            $groups = [];

            if ( count(Input::get('groups')) ) {
                foreach ( Input::get('groups') as $groupName ) {
                    if ( $group = Role::whereName($groupName)->first() ){
                        $groups[] = $group->id;
                    }
                }
            }
            else {
                $groups = $this->defaultRole;
            }

            $groups = count($groups) ? $groups : $this->defaultRole;

            $user = new User;
            $user->first_name = $inputs['first_name'];
            $user->last_name = $inputs['last_name'];
            $user->email = $inputs['email'];
            $user->username = Input::has('username') ? $inputs['username'] : NULL;
            $user->password = bcrypt( $inputs['password_confirmation'] );
            $user->active = 1;
            $user->save();

            $user->roles()->sync($groups);
            $user->groups = $user->roles;

            Cache::flush();
            return Response::json(['success' => 'User successfully saved', 'user' => $user]);
        }
    }


    public function update($id){
        $user = User::find($id);

        $inputs = [];
        foreach( Input::all() as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        if ( $inputs['email'] == $user->email )
            unset($this->rules['email']);
        if ( Input::has('username') && $inputs['username'] == $user->username )
            unset($this->rules['username']);

        $validation = Miscellaneous::validate($inputs, $this->rules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else {
            $groups = [];

            if ( count(Input::get('groups')) ) {
                foreach ( Input::get('groups') as $groupName ) {
                    if ( $group = Role::whereName($groupName)->first() ){
                        $groups[] = $group->id;
                    }
                }
            }
            else {
                $groups = $this->defaultRole;
            }

            $groups = count($groups) ? $groups : $this->defaultRole;

            $user->first_name = $inputs['first_name'];
            $user->last_name = $inputs['last_name'];
            $user->email = $inputs['email'];
            $user->username = Input::has('username') ? $inputs['username'] : NULL;
            if ( Input::has('password') )
                $user->password = bcrypt($inputs['password_confirmation']);
            $user->save();

            $user->roles()->sync($groups);
            $user->groups = $user->roles;

            Cache::flush();
            return Response::json(['success' => 'User successfully updated', 'updated_at' => $user->updated_at]);
        }
    }


}
