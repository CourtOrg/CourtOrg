<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Event;
use App\EventUser;
use App\User;
use DateTime;
use Session;

class EventController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

	public function storeEvent(Request $request)
	{
		$this->validate($request, [
            'type' => 'required',
            'start' => 'required',
            'start_time' => 'required|max:5|min:5',
            'end' => 'required',
            'end_time' => 'required|max:5|min:5',
        ]);

        // new 08.11.2017
        if($request->input('type') == "Veranstaltung")
        {
        	$this->validate($request, [
        		'event_name' => 'required'		
        ]);
        }

		$title = $request->input('title')." ".Auth::user()->last_name;
		$type = $request->input('type');
		$start = $request->input('start');
		$end = $request->input('end');
		$start_time = $request->input('start_time').":00";
		$end_time = $request->input('end_time').":00";
		$set_user = $request->input('set_user');
		// new 08.11.2017
		$event_name = $request->input('event_name');
	
		$start_dt = $start." ".$start_time;
		$end_dt = $end." ".$end_time;

		$can_save = Event::eventClash($start_dt,$end_dt,"");
		$dt_error = Event::dateTimeError($start_dt,$end_dt);

		if($can_save == true && $dt_error == false)
		{
			$event = new Event;
			// new 08.11.2017
			if($event_name == "")
			{
				$event->title = Auth::user()->last_name;
			}
			else
			{
				$event->title = $event_name;
			}
			
			$event->type = $type;
			$event->start = $start_dt;
			$event->end = $end_dt;
			$event->allDay = 0;
			$event->user_id = Auth::user()->id;
			$event->save();

			if($set_user == true)
			{
				$new_event = Event::where('start','=',$event->start)->first();

				$event_add_user = new Event;
				$event_add_user->id = $new_event->id;
				$event_add_user->title = Auth::user()->last_name;
				$event_add_user->type =  $type;
				$event_add_user->start =  $start;
				$event_add_user->start_time = $request->input('start_time');
				$event_add_user->end = $end;
				$event_add_user->end_time = $request->input('end_time');
				
				$users = "";

				Session::flash('success_with_user', '  Reservierung wurde erfolgreich gespeichert!');
				return view('event.add_user')->with('event',$event_add_user)->with('users',$users);
			}
			else
			{
				Session::flash('success', '  Reservierung wurde erfolgreich gespeichert!');
				return redirect('book_user');
			}

			
		}
		else
		{
			Session::flash('error', '  Es gibt eine Terminkollision bzw. einen Eingabefehler, überprüfen sie die möglichen freien Termine!');
			return redirect('book_user');
		}
	}
	public function show()
	{
		return view('event.show');
	}
	public function showEvent(Request $request)
	{
		$event_id = $request->input('id');
		$event = Event::findOrFail($event_id);
		$user_name = User::where('id','=',$event->user_id)->first();
		$event = Event::getEventData($event_id);
		$event_users = EventUser::where('event_id','=',$event_id)->get();
		$selected_users = EventUser::getSelectedUsers($event_users);
		$auth_check = Event::checkEventAuth($event_users);
		
		return view('event.show_event')->with('event',$event)
										->with('users',$selected_users)
										->with('user_name',$user_name)
										->with('event_users',$event_users)
										->with('auth_check',$auth_check);
	}
	public function newEvent(Request $request)
	{
		$start_data = $request->input('start');
		$is_past = Event::eventIsPast($start_data);

		if($is_past == false)
		{
			$event = new Event;
			$start_array = explode(" ",$start_data);
			$event->start = $start_array[0];
			$event->start_time = substr($start_array[1],0,5);
			$event->end = $start_array[0];
			$time = (int)substr($start_array[1],0,2);

			if( $time < 8)
			{
				$event->end_time = substr($start_array[1],0,2)+2 . substr($start_array[1],2,3);
				$event->end_time = "0" . $event->end_time;
			}
			else
			{
				$event->end_time = substr($start_array[1],0,2) +2 . substr($start_array[1],2,3);
			}
			return view('event.new')->with('event',$event);
		}
		else
		{
			Session::flash('date_error', '  Es können keine Reservierungen in der Vergangenheit angelegt werden!');
			return redirect('book_user');
		}
	}
	public function update(Request $request)
	{
		$id = $request->input('id');
		$title = $request->input('title');
		$start = $request->input('start');
		$end = $request->input('end');

		$event = Event::findOrFail($id);
		$event->title = $title;
		$event->start = $start;
		$event->end = $end;
		$event->save();

		return redirect()->back();
	}
	public function edit(Request $request)
	{

		$event_id = $request->input('id');
		$event = Event::getEventData($event_id);
		$event_users = EventUser::where('event_id','=',$event_id)->get();
		$selected_users = EventUser::getSelectedUsers($event_users);

		return view('event.edit')->with('event',$event)->with('users',$selected_users)->with('event_users',$event_users);
	}
	public function storeUpdateEdit(Request $request)
	{
		$this->validate($request, [
            'type' => 'required',
            'start' => 'required',
            'start_time' => 'required|max:5|min:5',
            'end' => 'required',
            'end_time' => 'required|max:5|min:5'
        ]);

		// new 08.11.2017
        if($request->input('type') == "Veranstaltung")
        {
        	$this->validate($request, [
        		'event_name' => 'required'		
        ]);
        }

		$id = $request->input('event');
        $type = $request->input('type');
		$start = $request->input('start');
		$end = $request->input('end');
		$start_time = $request->input('start_time').":00";
		$end_time = $request->input('end_time').":00";
		$set_user = $request->input('set_user');
		// new 08.11.2017
		$event_name = $request->input('event_name');
		//
		$start_dt = $start." ".$start_time;
		$end_dt = $end." ".$end_time;

		$can_save = Event::eventClash($start_dt,$end_dt,$id);
		$dt_error = Event::dateTimeError($start_dt,$end_dt);

		if($can_save == true && $dt_error == false)
		{
			$event = Event::findOrFail($id);
			$event->type = $type;
			$event->start = $start_dt;
			$event->end = $end_dt;
			// new 08.11.2017
			if($event->type == "Veranstaltung")
			{
				$event->title = $event_name;
			}
			else
			{
				$event->title = Auth::user()->last_name;
			}
			//
			$event->save();

			Session::flash('success', '  Reservierung wurde erfolgreich geändert!');
			return redirect('book_user');
		}
		else
		{
			Session::flash('error', '  Es gibt eine Terminkollision bzw. einen Eingabefehler, überprüfen sie die möglichen freien Termine!');
			return redirect('book_user');
		}
	}
	public function editEventUser(Request $request)
	{

		$event_id = $request->input('id');
		$event = Event::getEventData($event_id);
		$event_users = EventUser::where('event_id','=',$event_id)->get();
		$selected_users = EventUser::getSelectedUsers($event_users);

		return view('event.edit_event_user')->with('event',$event)->with('users',$selected_users)->with('event_users',$event_users);
	}
	public function saveEdit(Request $request)
	{
		/*
		$id = $request->input('id');
		$event = Event::findOrFail($id);

		$event = new Event;
		$event->name = $request->input('name');
		$event->title = $request->input('title');
		$event->start_time = $time[0];
		$event->end_time = $time[1];
		$event->save();


		$id = $request->input('id');
		$event = Event::find($id);
		$event->delete();
		
		return redirect('book_user');
		*/
	}
	public function delete(Request $request)
	{

		$id = $request->input('id');

		$event_user = EventUser::where("event_id","=",$id)->delete();

		$event = Event::find($id);
		$event->delete();
		Session::flash('delete', '  Reservierung wurde erfolgreich gelöscht!');
		return redirect('book_user');
	}
	public function backEvent()
	{
		return redirect('book_user');
	}
	public function calendarData()
    {
    	$time = new DateTime('now');
        $newtime = $time->modify('-7 day')->format('Y-m-d H:m:s');

        $data = Event::where('start','>',$newtime)->get();
        
        foreach($data as $d)
        {
        	$event_users = EventUser::where('event_id','=',$d->id)->where('change_event','=',1)->get();
        	
        	if($d->user_id == Auth::user()->id || Auth::user()->authority == "admin")
        	{
        		$d->editable = 1;
        		$d->borderColor = "#8ee198";
        	}
        	else
        	{
        		$d->borderColor = "#e18e8e";
        		$d->editable = 0;
        	}
        	if($d->type == "Internes Turnier")
        		{
        			$d->color = "#27a0f1";
        		}
        		elseif($d->type == "Externes Turnier")
        		{
        			$d->color = "#1de8f7";
        		}
        		elseif($d->type == "Veranstaltung")
        		{
        			$d->color = "#cece0d";
        		}
        		else
        		{
        			$d->color = "#8ee198";
        		}
        	if(empty($event_users))
        	{

        	}
        	else
        	{
        		foreach($event_users as $event_user)
        		{
        			if(Auth::user()->id == $event_user->user_id)
        			{
        				$d->editable = 1;
        				$d->borderColor = "#8ee198";
        			}
        		}
        	}

        }
        return response()->json($data);
    }
    




}
