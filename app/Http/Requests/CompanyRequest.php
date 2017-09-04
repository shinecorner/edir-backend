<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
        $rules = [
            'name' => 'required|max:191',
            'email' => 'required|email', //|unique:companies,email,' . $this->id,
            'phone' => 'max:50',
            'mobile' => 'max:50',
            'fax' => 'max:50',
            'www' => 'nullable|url|max:191',
            'summary' => 'nullable|max:250',
            'description' => 'nullable|max:2000',
            'video_url' => 'nullable|url',
            'address' => 'required',

            'monday.close_time' => 'nullable|after:monday.open_time|date_format:H:i|required_with:monday.open_time',
            'monday.open_time' => 'nullable|before:monday.close_time|date_format:H:i|required_with:monday.close_time',
            'monday.close_time_additional' => 'nullable|after:monday.open_time_additional|date_format:H:i|required_with:monday.open_time_additional',
            'monday.open_time_additional' => 'nullable|before:monday.close_time_additional|date_format:H:i|required_with:monday.close_time_additional',

            'tuesday.close_time' => 'nullable|after:tuesday.open_time|date_format:H:i|required_with:tuesday.open_time',
            'tuesday.open_time' => 'nullable|before:tuesday.close_time|date_format:H:i|required_with:tuesday.close_time',
            'tuesday.close_time_additional' => 'nullable|after:tuesday.open_time_additional|date_format:H:i|required_with:tuesday.open_time_additional',
            'tuesday.open_time_additional' => 'nullable|before:tuesday.close_time_additional|date_format:H:i|required_with:tuesday.close_time_additional',

            'wednesday.close_time' => 'nullable|after:wednesday.open_time|date_format:H:i|required_with:wednesday.open_time',
            'wednesday.open_time' => 'nullable|before:wednesday.close_time|date_format:H:i|required_with:wednesday.close_time',
            'wednesday.close_time_additional' => 'nullable|after:wednesday.open_time_additional|date_format:H:i|required_with:wednesday.open_time_additional',
            'wednesday.open_time_additional' => 'nullable|before:wednesday.close_time_additional|date_format:H:i|required_with:wednesday.close_time_additional',

            'thursday.close_time' => 'nullable|after:thursday.open_time|date_format:H:i|required_with:thursday.open_time',
            'thursday.open_time' => 'nullable|before:thursday.close_time|date_format:H:i|required_with:thursday.close_time',
            'thursday.close_time_additional' => 'nullable|after:thursday.open_time_additional|date_format:H:i|required_with:thursday.open_time_additional',
            'thursday.open_time_additional' => 'nullable|before:thursday.close_time_additional|date_format:H:i|required_with:thursday.close_time_additional',

            'friday.close_time' => 'nullable|after:friday.open_time|date_format:H:i|required_with:friday.open_time',
            'friday.open_time' => 'nullable|before:friday.close_time|date_format:H:i|required_with:friday.close_time',
            'friday.close_time_additional' => 'nullable|after:friday.open_time_additional|date_format:H:i|required_with:friday.open_time_additional',
            'friday.open_time_additional' => 'nullable|before:friday.close_time_additional|date_format:H:i|required_with:friday.close_time_additional',

            'saturday.close_time' => 'nullable|after:saturday.open_time|date_format:H:i|required_with:saturday.open_time',
            'saturday.open_time' => 'nullable|before:saturday.close_time|date_format:H:i|required_with:saturday.close_time',
            'saturday.close_time_additional' => 'nullable|after:saturday.open_time_additional|date_format:H:i|required_with:saturday.open_time_additional',
            'saturday.open_time_additional' => 'nullable|before:saturday.close_time_additional|date_format:H:i|required_with:saturday.close_time_additional',

            'sunday.close_time' => 'nullable|after:sunday.open_time|date_format:H:i|required_with:sunday.open_time',
            'sunday.open_time' => 'nullable|before:sunday.close_time|date_format:H:i|required_with:sunday.close_time',
            'sunday.close_time_additional' => 'nullable|after:sunday.open_time_additional|date_format:H:i|required_with:sunday.open_time_additional',
            'sunday.open_time_additional' => 'nullable|before:sunday.close_time_additional|date_format:H:i|required_with:sunday.close_time_additional',
        ];

        if (auth()->user()->can('manage-directory')) {
            $rules += [
                'listing_level' => 'required',
                'listing_valid_until' => 'date_format:d.m.Y|after:today',
            ];
        }

        return $rules;
    }
}
