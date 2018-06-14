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
use File;


class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
        ]);

        if($request->hasFile('avatar'))
        {
            $avatar = $request->file('avatar');
            $filename = time() . '.' . Str::lower($avatar->getClientOriginalExtension());
            $destinationPath = 'images/';
            // Try to get Meta Data of img
            try {
                $exif = exif_read_data($avatar);
            } catch ( \Exception $e){   
            }
            // make image and if nessesary rotate it error of ios und samsung phones
            $img = Image::make($avatar);
                    
                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 3:
                            $img->rotate(180);
                            break;
                            case 6:
                            $img->rotate(-90);
                            break;
                            case 8:
                            $img->rotate(90);
                        } 
                    }

            // Save img and change size
            $img->save($destinationPath.'/'.$filename,20);

            // Get file dimensions
            $img_dimensions = getimagesize($destinationPath.'/'.$filename);

             // Get deminsions of img
            $width = $img_dimensions[0];
            $height = $img_dimensions[1];

            // set oriantation of img for DB
            if($width > $height){
                $orientation = "width";
            }
            else{
                $orientation = "heigth";
            }
            //Image::make($avatar)->resize(300, 200)->save(public_path('images/' . $filename));
            if(Auth::user()->avatar != 'default.jpg')
            {
                File::Delete(public_path('/images/' . Auth::user()->avatar));
            }
            // Save into database
            $user = Auth::user();
            $user->avatar = $filename;
            $user->orientation = $orientation;
            $user->save();

            Session::flash('change_avatar', 'Profilbild wurde erfolgreich geändert!');
        }
        else
        {
            Session::flash('change_avatar_fail', 'Es ist ein Fehler aufgetreten, Benutzerbild wurde nicht geändert! Max. 5MB!');
        }
        return redirect()->action('UserController@update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        LaraFile::delete("images/{Auth::user()->avatar}");
    }
    public function delete()
    {

        
        $user = Auth::user();
        if(Auth::user()->avatar != 'default.jpg')
        {
            File::Delete(public_path('/images/' . Auth::user()->avatar));
            $user->avatar = 'default.jpg';
            $user->save();
            Session::flash('delete_photo', 'Benutzerbild wurde erfolgreich gelöscht!');
            return redirect()->back();
        }
        else
        {
            return redirect()->back();
        }
    }
}

