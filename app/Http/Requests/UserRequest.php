<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'gender' => 'required',
            'title' => 'nullable|max:50',
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191',
            'phone_number' => 'max:191',
            'email' => 'required|email|unique:users,email,' . $this->id,
            'password' => 'required_without:id|nullable|min:6',
            //'password' => 'required_without:id|nullable|min:6|confirmed',
            // 'password_confirmation' => 'required_with:password',
            'role' => (auth()->user()->isAdmin() ? 'required' : '')
        ];
    }
}
