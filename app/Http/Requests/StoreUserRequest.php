<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'user.username' => 'required|string|max:255|unique:users,username',
            'user.email' => 'required|string|email|max:255|unique:users,email',
            'user.password' => 'required|string|min:2',
        ];
    }

    public function messages()
    {
        return [
            'user.username.required' => 'Username is required!',
            'user.username.string' => 'Username must be a string!',
            'user.username.max' => 'Username cannot exceed 255 characters!',
            'user.username.unique' => 'Username already exists!',
            'user.email.required' => 'Email is required!',
            'user.email.string' => 'Email must be a string!',
            'user.email.email' => 'Email must be in email format!',
            'user.email.max' => 'Email cannot exceed 255 characters!',
            'user.email.unique' => 'Email already exists!',
            'user.password.required' => 'Password is required!',
            'user.password.string' => 'Password must be a string!',
            'user.password.min' => 'Password must be at least 8 characters!',
        ];
    }
}
