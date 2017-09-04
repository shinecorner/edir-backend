<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
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
            'title' => 'required|max:191',
            'description' => 'required|max:2000',
            'rating' => 'required|numeric|between:1,5',
            'approved' => 'boolean',
            'is_visible' => 'boolean',
            'ip_address' => 'ip',
            //'company_id' => '',
            //'directory_id' => '',
        ];
    }
}
