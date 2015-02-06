<?php namespace Kjamesy\Cms\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Event extends Eloquent
{
    protected $table = 'cms_events';

    public static $rules = [
        'first_name' => 'required|max:128',
        'last_name' => 'required|max:128',
        'email' => 'required|email',
        'organisation' => 'required|max:512',
        'title' => 'required|max:256',
        'venue' => 'required|max:256',
        'start_date' => 'required|date',
        'end_date' => 'required|date'
    ];

    protected static $cacheMinutes = 1440;

    public static function getEventResource() {
        return static::where('start_date', '>=', Carbon::now()->toDateString())->orderBy('start_date', 'asc')->get();
    }

    public static function getPastEventResource() {
        return static::where('start_date', '<', Carbon::now()->toDateString())->orderBy('start_date', 'desc')->get();
    }

    public static function getSingleEventResource($id) {
        return static::find($id);
    }


    public static function changeEventsStatus($ids, $action) {
        if ( count($ids) ) {
            if ( $action == 'destroy' ) {
                foreach ($ids as $id) {
                    $event = static::find($id);
                    if ( $event ) {
                        $event->delete();
                    }
                }
            }
            else {
                foreach ( $ids as $id ) {
                    $event = static::find($id);
                    if ( $event ) {
                        if ( $action == 'approve' )
                            $event->is_approved = 1;
                        elseif ( $action == 'unapprove' )
                            $event->is_approved = 0;
                        $event->save();
                    }
                }
            }

        }
    }
}