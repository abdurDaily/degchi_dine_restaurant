<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
        return [
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
        ];
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
