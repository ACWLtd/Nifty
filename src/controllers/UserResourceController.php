<?php namespace Kjamesy\Cms\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Kjamesy\Cms\Helpers\Miscellaneous;
use Kjamesy\Cms\Models\User;
use Sentinel\Repositories\Group\SentinelGroupRepositoryInterface;
use Sentinel\Repositories\User\SentinelUserRepositoryInterface;
use Sentinel\UserController;

class UserResourceController extends UserController {
    public function __construct(SentinelUserRepositoryInterface $userRepository, SentinelGroupRepositoryInterface $groupRepository){
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
        $this->user = $this->userRepository->retrieveById(Session::get('userId'));
        $this->rules = User::$rules;
        $this->newUserRules = User::$newUserRules;
        $this->defaultGroup = ['User' => 1];
    }

    public function index(){
        $users = $this->userRepository->all(); //User::getUserResource();
        $user = $this->user;
        $groups = $this->groupRepository->all();

        foreach($users as $aUser)
            $aUser->groups = $aUser->getGroups();

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
        else
        {
            $groups = [];

            if ( count(Input::get('groups')) ) {
                foreach ( Input::get('groups') as $selection )
                    $groups[$selection] = 1;
            }
            else {
                $groups = $this->defaultGroup;
            }

            $user = new User;
            $user->first_name = $inputs['first_name'];
            $user->last_name = $inputs['last_name'];
            $user->email = $inputs['email'];
            $user->username = Input::has('username') ? $inputs['username'] : NULL;
            $user->password = $inputs['password_confirmation'];
            $user->activated = 1;
            $user->save();

            $this->userRepository->changeGroupMemberships($user->id, $groups);
            $user->groups = $user->getGroups();

            Cache::flush();
            return Response::json(['success' => 'User successfully saved', 'user' => $user]);
        }
    }


    public function update($id){
        $user = $this->userRepository->retrieveById($id);

        $inputs = [];
        foreach( Input::all() as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        if ( $inputs['email'] == $user->email )
            unset($this->rules['email']);
        if ( Input::has('username') && $inputs['username'] == $user->username)
            unset($this->rules['username']);

        $validation = Miscellaneous::validate($inputs, $this->rules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else {
            $groups = [];

            if ( count(Input::get('groups')) ) {
                foreach ( Input::get('groups') as $selection )
                    $groups[$selection] = 1;
            }
            else {
                $groups = $this->defaultGroup;
            }

            $user->first_name = $inputs['first_name'];
            $user->last_name = $inputs['last_name'];
            $user->email = $inputs['email'];
            $user->username = Input::has('username') ? $inputs['username'] : NULL;
            if ( Input::has('password') )
                $user->password = $inputs['password_confirmation'];
            $user->save();

            $this->userRepository->changeGroupMemberships($id, $groups);

            Cache::flush();
            return Response::json(['success' => 'User successfully updated', 'updated_at' => $user->updated_at]);
        }
    }


}
