<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingRequest extends FormRequest
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
            'company' => 'required|string|max:250',
            'tagline' => 'required|string|max:250',
            'currency' => 'required|exists:currencies,id',
        ];
    }

    protected function passedValidation()
    {
        $this->replace($this->only([
            'company',
            'tagline',
            'currency',
        ]));
    }
}
