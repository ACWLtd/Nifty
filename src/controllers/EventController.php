<?php namespace Kjamesy\Cms\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Routing\Controller as BaseController;
use Kjamesy\Cms\Models\Event;
use Kjamesy\Cms\Models\User;

class EventController extends BaseController
{
    public function __construct() {
        $this->middleware('manage_content');
        $this->user = Auth::user();
        $this->isAdmin = User::isAdmin( $this->user );
        $this->logged_in_for = $this->user->last_login->diffForHumans();
        $this->rules = Event::$rules;
        $this->activeParent = 'events';
    }

    public function index() {
        return View::make("cms::events.angular", [
            'user' => $this->user,
            'isAdmin' => $this->isAdmin,
            'logged_in_for' => $this->logged_in_for,
            'activeParent' => $this->activeParent,
            'active' => 'allevents'
        ]);
    }

    public function do_bulk_actions($action) {
        try {
            $eventIds = Input::get('events');
            Event::changeEventsStatus($eventIds, $action);

            Cache::flush();

            return Response::json(['success' => "$action successful"]);
        } catch (Exception $e) {
            return Response::json(['error' => 'An error occurred'], 500);
        }
    }

}