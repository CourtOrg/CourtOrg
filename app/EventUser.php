<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\User;


class EventUser extends Model
{
    protected $table = 'event_users'; // you may change this to your name table
	public $timestamps = false; // set true if you are using created_at and updated_at
	protected $primaryKey = 'id'; // the default is id


	public static function userDuplicate($user_id,$event_id)
	{
		$event_users = EventUser::where('event_id','=',$event_id)->get();

		$user_duplicate = false;
		foreach($event_users as $event_user)
		{
			if($event_user->user_id == $user_id)
			{
				$user_duplicate = true;
			}
		}
		if($user_id == Auth::user()->id)
		{
			$user_duplicate = true;
		}
		return $user_duplicate;
	}
	public static function getSelectedUsers($event_users)
	{
		$i=0;
		foreach($event_users as $event_user)
		{
			$user_ids[$i] = ($event_user->user_id);
			$i++;
		}
		if(empty($user_ids))
		{
			$table = "";
		}
		else
		{
			$table = User::whereIn('id', $user_ids)->get();
		}
		return $table;
	}
}
