<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Event;
use App\EventUser;
use App\Album;
use Session;
use Hash;
use Illuminate\Support\Facades\DB;
use DateTime;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function logout()
    {
        Auth::logout();
        return view('home');
    }
    public function index()
    {
        return view('user.home_user');
    }
    public function show_all()
    {
        $user_type = "all";
        $count = User::getUsersCount();
        $users = User::getUserData($user_type,0);
        return view('user.show_users')->with('users',$users)->with('count',$count)->with('user_type',$user_type);
    }
    public function show_men()
    {
        $user_type = "men";
        $count = User::getUsersCount();
        $users = User::getUserData($user_type,0);
        return view('user.show_users')->with('users',$users)->with('count',$count)->with('user_type',$user_type);
    }
    public function show_woman()
    {
        $user_type = "woman";
        $count = User::getUsersCount();
        $users = User::getUserData($user_type,0);
        return view('user.show_users')->with('users',$users)->with('count',$count)->with('user_type',$user_type);
    }
    public function show_youth()
    {
        $user_type = "youth";
        $count = User::getUsersCount();
        $users = User::getUserData($user_type,0);
        return view('user.show_users')->with('users',$users)->with('count',$count)->with('user_type',$user_type);
    }
    public function show_search(Request $request)
    {
        $name = $request['name'];

        if($name == '')
        {
            $user_type = "all";
            $count = User::getUsersCount();
            $users = User::getUserData($user_type,0);
            return view('user.show_users')->with('users',$users)->with('count',$count)->with('user_type',$user_type);
        } 
        else
        {
            $user_type = "search";
            $count = User::getUsersCount();
            $users = User::getUserData($user_type, $name);
            return view('user.show_users')->with('users',$users)->with('count',$count)->with('user_type',$user_type);
        }
        
    }
    public function selectUserAll(Request $request)
    {
        $event = $request['event'];
        $type = $request['type'];

        $user_type = "all";
        $count = User::getUsersCount();
        $users = User::getUserData($user_type,0);
        return view('user.select_users')->with('users',$users)->with('count',$count)->with('user_type',$user_type)->with('event',$event)->with('type',$type);
        //return $event->id;
    }
    public function selectUserMen(Request $request)
    {
        $event = $request['event'];
        $type = $request['type'];

        $user_type = "men";
        $count = User::getUsersCount();
        $users = User::getUserData($user_type,0);
        return view('user.select_users')->with('users',$users)->with('count',$count)->with('user_type',$user_type)->with('event',$event)->with('type',$type); 
    }
    public function selectUserWoman(Request $request)
    {
        $event = $request['event'];
        $type = $request['type'];

        $user_type = "woman";
        $count = User::getUsersCount();
        $users = User::getUserData($user_type,0);
        return view('user.select_users')->with('users',$users)->with('count',$count)->with('user_type',$user_type)->with('event',$event)->with('type',$type); 
    }
    public function selectUserYouth(Request $request)
    {
        $event = $request['event'];
        $type = $request['type'];

        $user_type = "youth";
        $count = User::getUsersCount();
        $users = User::getUserData($user_type,0);
        return view('user.select_users')->with('users',$users)->with('count',$count)->with('user_type',$user_type)->with('event',$event)->with('type',$type);
    }
    public function selectSearch(Request $request)
    {
        $event = $request['event'];
        $name = $request['name'];
        $type = $request['type'];

        if($name == '')
        {
            $user_type = "all";
            $count = User::getUsersCount();
            $users = User::getUserData($user_type,0);
            return view('user.select_users')->with('users',$users)->with('count',$count)->with('user_type',$user_type)->with('event',$event)->with('type',$type);
        } 
        else
        {
            $user_type = "search";
            $count = User::getUsersCount();
            $users = User::getUserData($user_type, $name);
            return view('user.select_users')->with('users',$users)->with('count',$count)->with('user_type',$user_type)->with('event',$event)->with('type',$type);
        }
        
    }

    public function update()
    {
        $user = Auth::user();
        return view('user.update')->with('user',$user);
    }

    public function updateData(Request $request)
    {
        
        $this->validate($request, [
            'gender' => 'required',
            'first_name' => 'required|max:120',
            'last_name' => 'required|max:120',
            'date_of_birth' => 'required',
            'phone' => 'required|max:120'
        ]);
        
        $user = Auth::user();
        $user_data = new User;
        $gender = $request['gender'];
        $first_name = $request['first_name'];
        $last_name = $request['last_name'];
        $date_of_birth = $request['date_of_birth'];
        $phone = $request['phone'];

        $user_data->gender = $gender;
        $user_data->first_name = $first_name;
        $user_data->last_name = $last_name;
        $user_data->date_of_birth = $date_of_birth;
        $user_data->phone = $phone;

        $has_change = false;
        $has_name_change = false;
        if($user_data->gender != $user->gender)
        {
            $user->gender = $user_data->gender;
            Session::flash('message_gender', 'Geschlecht wurde erfolgreich geändert!');
            $has_change = true;
        }
        if($user_data->first_name != $user->first_name)
        {
            $user->first_name = $user_data->first_name;
            Session::flash('message_first_name', 'Vorname wurde erfolgreich geändert!');
            $has_change = true;
            $has_name_change = true;
        }
        if($user_data->last_name != $user->last_name)
        {
            $user->last_name = $user_data->last_name;
            Session::flash('message_last_name', 'Nachname wurde erfolgreich geändert!');
            $has_change = true;
            $has_name_change = true;
        }
        if($user_data->date_of_birth != $user->date_of_birth)
        {
            $user->date_of_birth = $user_data->date_of_birth;
            Session::flash('message_date_of_birth', 'Geburtsdatum wurde erfolgreich geändert!');
            $has_change = true;
        }
        if($user_data->phone != $user->phone)
        {
            $user->phone = $user_data->phone;
            Session::flash('message_phone_number', 'Telefonnummer wurde erfolgreich geändert!');
            $has_change = true;
        }
        
        if($has_change == true)
        {
            if($has_name_change == true)
            {
                $user->name = $user_data->last_name ." ".$user_data->first_name;
            }
            $user->save();

            return redirect()->back();
        }
        Session::flash('message_no_changes', 'Keine Änderungen durchgeführt!');
        return redirect()->back();
        

    }
    public function updateEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4'
        ]);

        $user = Auth::user();
        $user_data = new User;

        $email = $request['email'];
        $user_data->email = $email;

        if($user_data->email != $user->email)
        {
            if (Hash::check($request['password'],$user->password))
            {
                $user->email = $user_data->email;
                Session::flash('message_password_right', 'E-Mail Adresse wurde erflogreich geändert!');
                $user->save();
                return redirect()->back();
            }
            else
            {
                Session::flash('message_password_error', 'E-Mail Adresse konnte nicht geändert werden, falsches Passwort!');
                return redirect()->back();
            }
        }
    }
    public function updatePassword(Request $request)
    {
        $this->validate($request, [
        'password_old' => 'required|min:4',
        'password_new' => 'required|min:4',
        'password_confirm' => 'required|min:4|same:password_new'
        ]);

        $user = Auth::user();

        if (Hash::check($request['password_old'],$user->password))
        {
            $user->password = bcrypt($request['password_new']);
            $user->save();
            Session::flash('message_password_changed', 'Password wurde erflogreich geändert!');
            return redirect()->back();
        }
        else
        {
            Session::flash('message_password_change_error', 'Passwort konnte nicht geändert werden, falsches Passwort!');
        }

        
    }
    public function delete(Request $request)
    {
        $id = $request['id'];

        $event_ids = Event::getEventIdsByUserId($id);

        // 1. Alle EventUser Teilnahmen löschen wo dieser User eingeladen wurde
        EventUser::where("user_id","=",$id)->delete();

        // 2. Alle EverUser Teilmehmer löschen die an einem erstellten Event vom User hängen
        foreach($event_ids as $event_id)
        {
            EventUser::where("event_id","=",$event_id->id)->delete();
        }

        // 3. Alle Events die von diesem User erst wurden löschen
        
        foreach($event_ids as $event_id)
        {
            Event::where("id","=",$event_id->id)->delete();
        }

        // 4. User selber löschen
        User::where("id","=",$id)->delete();

        return redirect()->back();
        
    }
    public function edit(Request $request)
    {
        $id = $request['user_id'];
        $user = DB::table('users')->where('id','=',$id)->get();
        //print_r($user);
        return view('user.admin_edit')->with('user',$user);
    }
    public function showEventUser()
    {
        return view('user.event_user');
    }
    public function showPhotosUser()
    {
        $albums = Album::get();

        //print_r($albums);
        return view('user.photo_user')->with('albums',$albums);
    }
    public function showAboutUsUser()
    {
        return view('user.about_us_user');
    }
}