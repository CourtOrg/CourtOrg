<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Session;
use Hash;
use Illuminate\Support\Facades\DB;
use DateTime;

use App\Photo;
use App\Album;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*
    public function __construct()
    {
        $this->middleware('auth');
    }
    */
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view('home');
    }
    public function showTest()
    {

        $album_id = 44;
        $photos = Photo::where('album_id','=',$album_id)->get();
        $album = Album::where('id','=', $album_id)->first();
        return view('test')->with('photos', $photos)->with('album', $album);
    }
    public function showEvents()
    {
        return view('guest.events');
    }
    public function show_all()
    {
        $user_type = "all";
        $count = User::getUsersCount();
        $users = User::getUserData($user_type,0);
        return view('guest.show_members')->with('users',$users)->with('count',$count)->with('user_type',$user_type);
    }
    public function show_men()
    {
        $user_type = "men";
        $count = User::getUsersCount();
        $users = User::getUserData($user_type,0);
        return view('guest.show_members')->with('users',$users)->with('count',$count)->with('user_type',$user_type);
    }
    public function show_woman()
    {
        $user_type = "woman";
        $count = User::getUsersCount();
        $users = User::getUserData($user_type,0);
        return view('guest.show_members')->with('users',$users)->with('count',$count)->with('user_type',$user_type);
    }
    public function show_youth()
    {
        $user_type = "youth";
        $count = User::getUsersCount();
        $users = User::getUserData($user_type,0);
        return view('guest.show_members')->with('users',$users)->with('count',$count)->with('user_type',$user_type);
    }
    public function showPhotos()
    {
        return view('guest.photos');
    }
    public function showAboutUs()
    {
        return view('guest.about_us');
    }
}
