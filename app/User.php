<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DateTime;
use Illuminate\Support\Facades\DB;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'authority','gender','first_name','last_name','date_of_birth', 'email','avatar', 'phone', 'password','name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public static function getUsersCount()
    {
        $time = new DateTime('now');
        $newtime = $time->modify('-16 year')->format('Y-m-d');
        $count = (object) array('all' => 'male', 'female' => 'youth');
        $count->all = count(User::all());
        $count->male = count(User::where('gender','=', 'male')->where('date_of_birth','<',$newtime)->get());
        $count->female = count(User::where('gender', 'female')->where('date_of_birth','<',$newtime)->get());
        $count->youth = count(User::where('date_of_birth','>',$newtime)->get());
        
    return $count;
    }
        public static function getUserData($user_type, $name)
    {
        $time = new DateTime('now');
        $newtime = $time->modify('-16 year')->format('Y-m-d');
        if($user_type == 'all')
        {
            $users = User::orderby('last_name')->paginate(10);
        }
        if($user_type == 'men')
        {
            $users = User::where('date_of_birth','<',$newtime)->where('gender','=','male')->orderby('last_name')->paginate(10);
        }
        if($user_type == 'woman')
        {
            $users = User::where('date_of_birth','<',$newtime)->where('gender','=','female')->orderby('last_name')->paginate(10);
        }
        if($user_type == 'youth')
        {
            $users = User::where('date_of_birth','>',$newtime)->orderby('last_name')->paginate(10);
        }
        if($user_type == 'search')
        {
            $users = User::where('name','=',$name)->orderby('last_name')->paginate(1);
        }
    return $users;

    }
}
