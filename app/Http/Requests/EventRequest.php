<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
			'summary' => 'nullable|max:250',
            'address' => 'required',
			'description' => 'required',
			'date_start' => 'date_format:d.m.Y',
			'date_end' => 'date_format:d.m.Y|after_or_equal:date_start',
			'time_start' => 'nullable|date_format:H:i',
			'time_end' => 'nullable|date_format:H:i',
			'discount_type' => 'required|in:none,fixed,percent',
			'discount_value' => 'required_unless:discount_type,none',
			'discount_coupon' => '',
			'regular_price' => 'required|numeric',
			// 'image' => 'nullable|image|max:2048',
			'video_url' => 'nullable|url',
			// 'status' => '', todo ??? was is status
			'valid_until' => 'date_format:d.m.Y|after:today',
			'category_event_id' => 'required',
			'company_id' => 'required',
        ];
    }
}
