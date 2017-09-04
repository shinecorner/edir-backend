<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DealRequest extends FormRequest
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
            // 'address' => 'required|max:191', // todo virtual address field neded (return data from map)
			'summary' => 'nullable|max:250',
			'description' => 'required',
			'conditions' => 'nullable',
			'date_start' => 'date_format:d.m.Y',
			'date_end' => 'date_format:d.m.Y|after_or_equal:date_start',
			'discount_type' => 'required|in:none,fixed,percent',
			'discount_value' => 'required_unless:discount_type,none',
			'regular_price' => 'numeric|required',
			// 'image' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url',
            'product_url' => 'nullable',
            'discount_coupon' => 'nullable',
			'category_deal_id' => 'required',
			'company_id' => 'required',
        ];
    }
}
