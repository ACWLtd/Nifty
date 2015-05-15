<?php namespace Kjamesy\Cms\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Kjamesy\Cms\Helpers\Miscellaneous;
use Kjamesy\Cms\Models\Event;
use Sentinel\Repositories\User\SentinelUserRepositoryInterface;

class EventResourceController extends Controller {
    public function __construct(SentinelUserRepositoryInterface $userRepository){
        $this->user = $userRepository->retrieveById(Session::get('userId'));
        $this->rules = Event::$rules;
    }

    public function index(){
        $user = $this->user;
        $events = Event::getEventResource();
        $past_events = Event::getPastEventResource();

        return Response::json(compact('user', 'events', 'past_events'));
    }


    public function store(){
        $inputs = [];
        foreach( Input::all() as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        $validation = Miscellaneous::validate($inputs, $this->rules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else {
            $event = new Event;
            $event->is_approved = $inputs['is_approved'];
            $event->first_name = $inputs['first_name'];
            $event->last_name = $inputs['last_name'];
            $event->email = $inputs['email'];
            $event->organisation = $inputs['organisation'];
            $event->title = $inputs['title'];
            $event->venue = $inputs['venue'];
            $event->description = $inputs['description'];
            $event->type = $inputs['type'];
            $event->start_date = Carbon::createFromFormat('Y-m-d', $inputs['start_date'])->toDateString();
            $event->end_date = Carbon::createFromFormat('Y-m-d', $inputs['end_date'])->toDateString();
            $event->save();

            Cache::flush();

            return Response::json(['success' => 'Event successfully saved', 'id' => $event->id]);
        }
    }

    public function show($id){
        $event = Event::getSingleEventResource($id);

        return Response::json(compact('event'));
    }


    public function update($id){
        $inputs = [];
        foreach( Input::all() as $key => $input ) {
            if ( ! is_array($input) )
                $inputs[$key] = trim($input);
        }

        $validation = Miscellaneous::validate($inputs, $this->rules);

        if( $validation !== true )
            return Response::json(['validation' => $validation]);
        else
        {
            $event = Event::find($id);
            $event->is_approved = $inputs['is_approved'];
            $event->first_name = $inputs['first_name'];
            $event->last_name = $inputs['last_name'];
            $event->email = $inputs['email'];
            $event->organisation = $inputs['organisation'];
            $event->title = $inputs['title'];
            $event->venue = $inputs['venue'];
            $event->description = $inputs['description'];
            $event->type = $inputs['type'];
            $event->start_date = Carbon::createFromFormat('Y-m-d', $inputs['start_date'])->toDateString();
            $event->end_date = Carbon::createFromFormat('Y-m-d', $inputs['end_date'])->toDateString();
            $event->save();

            Cache::flush();

            return Response::json(['success' => 'Event successfully updated']);
        }
    }


}
