<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Session;
use Hash;
use Image;
use Illuminate\Support\Facades\File as LaraFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use File;
use App\Album;
use App\Photo;



class AlbumsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(){
        
        $albums = Album::orderBy('created_at')->get();
        $albums = $albums->reverse();
        return view('albums.show')->with('albums',$albums);
    }

    public function store(Request $request){
    	
    	$this->validate($request, [
    		'name' => 'required|unique:albums',
    	]);
        $album = new Album;
        $album->user_id = Auth::user()->id;
        $album->name = $request->name;
        $album->save();

    	return redirect()->back();
    }

    public function drop(Request $request){
        // Alle Photos in DB und public Ordner löschen die in diesem Album gespeichert wurden
        Photo::where("album_id","=",$request->id)->delete();
        File::deleteDirectory(public_path('img/albums/'.$request->id));
        // Album in DB löschen
        Album::where("id","=",$request->id)->delete();
        $albums = Album::orderBy('created_at')->get();
        $albums = $albums->reverse();
        return redirect('albums_user')->with('albums',$albums);
    }

    public function changeAlbumName(Request $request){

        $this->validate($request, [
        'name' => 'required|unique:albums',
        ]);

        $album = Album::findOrFail($request->id);
        $album->name = $request->name;
        $album->save();

        return redirect()->back();
    }

}
