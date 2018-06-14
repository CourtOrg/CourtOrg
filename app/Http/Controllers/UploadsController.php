<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
use Response;
use Redirect;
use Session;
use App\Uploads;


class UploadsController extends Controller {

	public function index() {
		//return 'hallo';
		return view('upload.index');
	}



	public function multiple_upload(Request $request) {
		// get all of the post data
		$files = $request->file('file');
		print_r($files);
		//$files = Input::file('images');
		// Making counting of uploaded images
		$file_count = count($files);
		// start count how many uploades
		$uploadcount = 0;

		foreach ($files as $file) {
			$rules = array('file' => 'required'); // 'required'|mimes:png:gif:jpeg:txt:pdf:
			$validator = validator::make(array('file' => $file), $rules);
			if($validator->passes()){
				$destinationPath = 'uploads'; // upload folder in public directory
				$filename = $file->getClientOriginalName();
				$upload_success = $file->move($destinationPath, $filename);
				$uploadcount++;

				// save into database
				$extention = $file->getClientOriginalExtension();
				$entry = new Uploads;
				$entry->mime = $file->getClientMimeType();
				$entry->original_filename = $filename;
				$entry->filename = $file->getFilename().'.'.$extention;
				$entry->save();
			}
		}
		if($uploadcount == $file_count) {
			Session::flash('success', 'Upload successfully');
			return Redirect::to('upload');
		} else {
			return Redirect::to('upload')->withInput()->withErrors($validator);
		}

	}
    
}
