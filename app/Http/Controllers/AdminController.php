<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Session;
use Hash;
use Illuminate\Support\Facades\DB;
use DateTime;
use Validator;



class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function update(Request $request)
    {
    	$id = $request['user_id'];
    	$user = DB::table('users')->where('id','=',$id)->first();
    	return view('admin.update')->with('user',$user);
    }
    public function updateUserData(Request $request)
    {
        $id = $request['user_id'];

        $user = DB::table('users')->where('id','=',$id)->first();
        $user_data = new User;
        $gender = $request['gender'];
        $authority = $request['authority'];
        $first_name = $request['first_name'];
        $last_name = $request['last_name'];
        $date_of_birth = $request['date_of_birth'];
        $phone = $request['phone'];

        $user_data->gender = $gender;
        $user_data->authority = $authority;
        $user_data->first_name = $first_name;
        $user_data->last_name = $last_name;
        $user_data->date_of_birth = $date_of_birth;
        $user_data->phone = $phone;

        $user = DB::table('users')->where('id','=',$id)->first();
        
        $has_change = false;
        $has_name_change = false;
        if($user_data->gender != $user->gender)
        {
            DB::table('users')->where('id','=',$id)->update(['gender' => $gender]);
            //Session::flash('message_gender', 'Geschlecht wurde erfolgreich geändert!');
            $has_change = true;
        }
        if($user_data->authority != $user->authority)
        {
            DB::table('users')->where('id','=',$id)->update(['authority' => $authority]);
            //Session::flash('message_gender', 'Geschlecht wurde erfolgreich geändert!');
            $has_change = true;
        }
        if($user_data->first_name != $user->first_name)
        {
            DB::table('users')->where('id','=',$id)->update(['first_name' => $first_name]);
            //Session::flash('message_first_name', 'Vorname wurde erfolgreich geändert!');
            $has_change = true;
            $has_name_change = true;
        }
        if($user_data->last_name != $user->last_name)
        {
            DB::table('users')->where('id','=',$id)->update(['last_name' => $last_name]);
            //Session::flash('message_last_name', 'Nachname wurde erfolgreich geändert!');
            $has_change = true;
            $has_name_change = true;
        }
        if($user_data->date_of_birth != $user->date_of_birth)
        {
            DB::table('users')->where('id','=',$id)->update(['date_of_birth' => $date_of_birth]);
            //Session::flash('message_date_of_birth', 'Geburtsdatum wurde erfolgreich geändert!');
            $has_change = true;
        }
        if($user_data->phone != $user->phone)
        {
            DB::table('users')->where('id','=',$id)->update(['phone' => $phone]);
            //Session::flash('message_phone_number', 'Telefonnummer wurde erfolgreich geändert!');
            $has_change = true;
        }
        
        if($has_change == true)
        {
            if($has_name_change == true)
            {
                DB::table('users')->where('id','=',$id)->update(['name' => $user_data->last_name ." ".$user_data->first_name]);
            }

            $user_2 = DB::table('users')->where('id','=',$id)->first();
            return view('admin.update')->with('user',$user_2);
        }
		return view('admin.update')->with('user',$user);

    }


}
