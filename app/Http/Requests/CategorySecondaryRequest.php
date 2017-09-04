<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategorySecondaryRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|max:191',
            //slug
            //count
            //'image' => 'image|max:2048',
            'description' => 'nullable',
            'category_primary_id' => 'required',
            //seo_meta_title
            //seo_meta_description
        ];
    }
}
