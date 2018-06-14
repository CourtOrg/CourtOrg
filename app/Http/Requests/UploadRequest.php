<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules_test()
    {
        $rules = [
            'attachments' => 'required|max:2560'
        ];
        $photos = count($this->file('attachments'));
        foreach(range(0, $photos) as $index) {
            //$rules['attachments.' . $index] = 'image|mimes:jpeg,bmp,png|max:2000';
            $rules['attachments.' . $index] = 'image|mimes:jpeg,bmp,png';
        }
 
        return $rules;
    }
    public function rules()
    {
        $rules = [
            'file' => 'image|mimes:jpeg,bmp,png';
        ];
 
        return $rules;
    }
}
