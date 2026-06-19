<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user')?->id?? null;

        $rules = array();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'nullable|array|min:1',
            'roles.*' => 'nullable|string|exists:roles,name',
            'status' => 'nullable|boolean',
        ];

        if($this->method() == 'POST')
        {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        elseif($this->method() == 'PUT')
        {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'image.image' => 'The profile image must be an image file.',
            'image.mimes' => 'The profile image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The profile image may not be greater than 2MB.',
        ];
    }
}
