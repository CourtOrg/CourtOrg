<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DateTime;


class Event extends Model
{
    protected $table = 'events'; // you may change this to your name table
	public $timestamps = false; // set true if you are using created_at and updated_at
	protected $primaryKey = 'id'; // the default is id

	public static function eventClash($start, $end, $event_id)
	{
		$can_save = true;
		$time_now = new DateTime('now');

		if(empty($event_id))
		{
	 		$events = Event::where('start','>',$time_now)->get();
		}
		else
		{
	 		$events = Event::where('start','>',$time_now)->where('id','!=',$event_id)->get();

		}
		foreach ($events as $event) {
	 		if($end <= $event->start || $start >= $event->end)
	 		{
	 			
	 		}
	 		else
		 	{
		 		$can_save = false;
		 	}
	 	}
	 	return $can_save;
	}
	public static function dateTimeError($start, $end)
	{
		$dt_error = false;
		if($start == $end || $start > $end)
		{
			$dt_error = true;
		}
		return $dt_error;
	}
	public static function eventIsPast($start)
	{
		$time = new DateTime('now');
		$newtime = $time->modify('-1 hour')->format('Y-m-d H:m:s');

	 	$is_past = false;

	 	if($start < $newtime)
	 	{
	 		$is_past = true;
	 	}
	 	return $is_past;
	}
	public static function getEventData($event_id)
	{
		$event = Event::findOrFail($event_id);
		$start = explode(" ",$event->start);
		$event->start = $start[0];
		$event->start_time = substr($start[1],0,5);

		$end = explode(" ",$event->end);
		$event->end = $end[0];
		$event->end_time = substr($end[1],0,5);
		
		return $event;
	}
	public static function checkEventAuth($event_users)
	{
		$auth_check = 0;
		if(empty($event_users))
		{
			
		}
		else
		{
			foreach($event_users as $event_user)
			{
				if(Auth::user()->id == $event_user->user_id && $event_user->change_event == 1)
				{
				$auth_check = 1;
				}
			}
		}
		return $auth_check;
	}
	public static function changeEventTitle($count,$selected_users,$event_id)
	{
		if($count == 1)
		{
			foreach($selected_users as $selected_user)
			{
				$event_new = Event::where('id','=',$event_id)->first();
				$user_name = User::where('id','=',$event_new->user_id)->first();
				$event_new->title = $user_name->last_name . " - " . $selected_user->last_name;
				$event_new->save();
			}
		}
		elseif($count == 0)
		{
			$event_new = Event::where('id','=',$event_id)->first();
			$user_name = User::where('id','=',$event_new->user_id)->first();
			$event_new->title = $user_name->last_name;
			$event_new->save();
		}
		else
		{
			$event_new = Event::where('id','=',$event_id)->first();
			$user_name = User::where('id','=',$event_new->user_id)->first();
			$event_new->title = $user_name->last_name . " - " . "...";
			$event_new->save();
		}
	}
	public static function getEventIdsByUserId($user_id)
	{
		$event_ids = Event::where('user_id','=',$user_id)->select('id')->get();
		return $event_ids;
		
	}

}
