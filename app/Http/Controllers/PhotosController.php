<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Photo;
use App\Album;
use Illuminate\Support\Facades\File as LaraFile;
use Illuminate\Support\Facades\Storage;
use File;
// new 08.01.2018
use Image;
use Illuminate\Http\UploadedFile;
use DateTime;
use App\Http\Requests\UploadRequest;


class PhotosController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request){

    	$album_id = $request->id;
    	$photos = Photo::where('album_id','=',$album_id)->get();
    	$album = Album::where('id','=', $album_id)->first();
    	
    	return view ('photos.show')->with('photos', $photos)->with('album', $album);
    }

    public function upload(Request $request){

    	$album_id = $request->id;
    	$album = Album::where('id','=', $album_id)->first();
    	
    	return view ('photos.upload')->with('album', $album);
    }

    public function deletePhotos(Request $request){

    	$set_imgs = $request->set_img;
    	$album_id = $request->album_id;

    	foreach($set_imgs as $set_img){
    		// Photo in DB
    		$photo = Photo::where("id","=",$set_img)->first();
    		// Pfad und Name
    		$path_name = 'img/albums/'.$album_id.'/'.$photo->photo;
    		// Photo löschen
    		File::delete($path_name);
    		// Photoe in DB löschen
    		Photo::where("id","=",$set_img)->delete();
    	}
    	// change cover_image from album
	   	$posible_cover_image = Photo::where('album_id','=',$album_id)->first();
	   	$album = Album::findOrFail($album_id);
	    	if(!empty($posible_cover_image)){
	    		$album->cover_image = 'albums/'.$album_id.'/'.$posible_cover_image->photo;
	    		$album->cover_image_orienation = $posible_cover_image->orientation;
	    		$album->save();
	    	}
	    	else{
	    		$album->cover_image = 'default_album.png';
	    		$album->cover_image_orienation = 'width';
	    		$album->save();
	    	}

    	return redirect()->back();
    }

    public function selectPhotos(Request $request){
    	$album_id = $request->id;
    	$photos = Photo::where('album_id','=',$album_id)->get();
    	$album = Album::where('id','=', $album_id)->first();
    	return view ('photos.select')->with('photos', $photos)->with('album', $album);
    }

    public function postUpload(Request $request){

    	
    	$this->validate($request, [
            'file' => 'image|mimes:jpeg,bmp,png'
        ]);
    	// Get album id
    	$album_id = $request->album_id;


	      		if(!File::exists(public_path('img/albums/'.$album_id))) {
    				File::makeDirectory(public_path('img/albums/'.$album_id));
				}


    	$image = $request->file('file');
        $imageName = time().$image->getClientOriginalName();


        $destinationPath = 'img/albums/'.$album_id;


        // Try to get Meta Data of img
		try {
  			$exif = exif_read_data($image);
		} catch ( \Exception $e){	
		}
		// make image and if nessesary rotate it error of ios und samsung phones
        $img = Image::make($image);
	      		
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
        $img->save($destinationPath.'/'.$imageName,20);

       	// Get file dimensions
	    $img_dimensions = getimagesize($destinationPath.'/'.$imageName);

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
       	// Save into database
	    $photos = new Photo();
	    $photos->album_id = $album_id;
	    $photos->photo = $imageName;
	    $photos->orientation = $orientation;
	    $photos->save();

	   	// change cover_image from album if nessesary
	   	$posible_cover_image = Photo::where('album_id','=',$album_id)->first();
	    if(!empty($posible_cover_image)){
	    	$album = Album::findOrFail($album_id);
	    	$album->cover_image = 'albums/'.$album_id.'/'.$posible_cover_image->photo;
	    	$album->cover_image_orienation = $posible_cover_image->orientation;
	    	$album->save();
	    }
	}


}
