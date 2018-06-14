<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\EventUser;
use App\Event;
use App\User;
use Session;

class EventUserController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
    	// Input
    	$type = $request->input('type');
		$user_id = $request->input('user_id');
		$event_id = $request->input('event');

		// Function
		$event = Event::getEventData($event_id);

		$event_users = EventUser::where('event_id','=',$event_id)->get();

		$selected_users = EventUser::getSelectedUsers($event_users);

		$user_duplicate = EventUser::userDuplicate($user_id,$event_id);


		if($user_duplicate == true)
		{
			if($type == "edit")
			{
				Session::flash('user_duplicate', '  Sie können eine Person nur einmal auswählen, bzw. können sie sich nicht selbst auswählen!');
				return view('event.edit_event_user')->with('event',$event)->with('users',$selected_users)->with('event_users',$event_users);
			}
			else
			{
				Session::flash('user_duplicate', '  Sie können eine Person nur einmal auswählen, bzw. können sie sich nicht selbst auswählen!');
				return view('event.add_user')->with('event',$event)->with('users',$selected_users);
			}
		}
		else
		{
		$event_user = new EventUser;

        $event_user->event_id = $request->input('event');
        $event_user->user_id = $request->input('user_id');
        $event_user->change_event = false;
        $event_user->save();

        $event_users = EventUser::where('event_id','=',$event_id)->get();
		$selected_users = EventUser::getSelectedUsers($event_users);

		$count = $selected_users->count();

		// new 08.11.2017
		if($event->type != "Veranstaltung")
		{	
			// war früher ohne if
			Event::changeEventTitle($count,$selected_users,$event_id);
		}
		//
			if($type == "edit")
			{
				return view('event.edit_event_user')->with('event',$event)->with('users',$selected_users)->with('event_users',$event_users);
			}
			else
			{
				return view('event.add_user')->with('event',$event)->with('users',$selected_users);
			}

		}
    }
    public function updateEditUser(Request $request)
	{
		$event_id = $request->input('event');
		$auth_ids = $request->input('set_auth');
		
		$event_users = EventUser::where('event_id','=',$event_id)->get();


		if(empty($event_users))
		{
			Session::flash('no_users', '  Es sind keine Mitspieler vorhanden!');
		}
		else
		{
			if(empty($auth_ids))
			{
				foreach($event_users as $event_user)
				{
					$event = EventUser::where('event_id','=',$event_id)->where('user_id','=',$event_user->user_id)->first();
					$event->change_event = false;
					$event->save();
				}
				
			}
			else
			{
				foreach($event_users as $event_user)
				{
					$checked = false;
					foreach($auth_ids as $auth_id)
					{
						if($auth_id == $event_user->user_id)
						{
							$checked = true;
						}
					}
					if($checked == true)
					{
						$event = EventUser::where('event_id','=',$event_id)->where('user_id','=',$event_user->user_id)->first();
						$event->change_event = true;
						$event->save();
					}
					else
					{
						$event = EventUser::where('event_id','=',$event_id)->where('user_id','=',$event_user->user_id)->first();
						$event->change_event = false;
						$event->save();
					}
				}
					

			}	
		}

		$event = Event::getEventData($event_id);
		$user_name = User::where('id','=',$event->user_id)->first();
		$event = Event::getEventData($event_id);
		$event_users = EventUser::where('event_id','=',$event_id)->get();
		$selected_users = EventUser::getSelectedUsers($event_users);

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
		
		return view('event.show_event')->with('event',$event)
										->with('users',$selected_users)
										->with('user_name',$user_name)
										->with('event_users',$event_users)
										->with('auth_check',$auth_check);

	}
    public function delete(Request $request)
    {
    	$event_id = $request->input('id');
		$user_id = $request->input('user_id');
		
		
		$event = Event::getEventData($event_id);
		$event_user = EventUser::where("event_id","=",$event_id)->where('user_id','=',$user_id)->delete();
		$event_users = EventUser::where('event_id','=',$event_id)->get();
		$selected_users = EventUser::getSelectedUsers($event_users);
		
		return view('event.add_user')->with('event',$event)->with('users',$selected_users);

    }
    public function setAuth(Request $request)
    {
    	$event_id = $request->input('event');
		
		
		$event = Event::getEventData($event_id);
		$event_users = EventUser::where('event_id','=',$event_id)->get();
		$selected_users = EventUser::getSelectedUsers($event_users);
		
		return view('event.set_auth')->with('event',$event)->with('users',$selected_users);
    }
        public function editEventUserAuth(Request $request)
    {
		$event_id = $request->input('event');
		$event = Event::getEventData($event_id);
		$event_users = EventUser::where('event_id','=',$event_id)->get();
		$selected_users = EventUser::getSelectedUsers($event_users);

		return view('event.edit_event_user_auth')->with('event',$event)->with('users',$selected_users)->with('event_users',$event_users);
    }



    public function deleteEdit(Request $request)
    {
    	$event_id = $request->input('id');
		$user_id = $request->input('user_id');
		
		
		$event = Event::getEventData($event_id);
		$event_user = EventUser::where("event_id","=",$event_id)->where('user_id','=',$user_id)->delete();
		$event_users = EventUser::where('event_id','=',$event_id)->get();
		$selected_users = EventUser::getSelectedUsers($event_users);
		
		if($selected_users != "")
		{
			$count = $selected_users->count();
		}
		else
		{
			$count = 0;
		}
		// new 08.11.2017
		if($event->type != "Veranstaltung")
		{
			// war früher ohne if
			Event::changeEventTitle($count,$selected_users,$event_id);	
		}
		//
		return view('event.edit_event_user')->with('event',$event)->with('users',$selected_users)->with('event_users',$event_users);

    }
    public function saveAuth(Request $request)
    {
    	$event_id = $request->input('event');
		$auth_ids = $request->input('set_auth');
		
		if(empty($auth_ids))
		{
			Session::flash('user_auth_none', '  Es wurden keine Berechtigungen vergeben!');
		}
		else
		{
			foreach($auth_ids as $auth_id)
			{
				$event = EventUser::where('event_id','=',$event_id)->where('user_id','=',$auth_id)->first();
				$event->change_event = true;
				$event->save();
			}
			Session::flash('user_auth', '  Die Berechtigungen wurden erfolgreich gespeichert!');

		}
		
		return redirect('book_user');

    }
    public function backToAddUser(Request $request)
    {
    	$type = $request->input('type');
		$user_id = $request->input('user_id');
		$event_id = $request->input('event');

		// Function
		$event = Event::getEventData($event_id);

		$event_users = EventUser::where('event_id','=',$event_id)->get();

		$selected_users = EventUser::getSelectedUsers($event_users);

		if($type == "edit")
		{
			return view('event.edit_event_user')->with('event',$event)->with('users',$selected_users)->with('event_users',$event_users);
		}
		else
		{
			return view('event.add_user')->with('event',$event)->with('users',$selected_users);
		}
		
		
    }


}
