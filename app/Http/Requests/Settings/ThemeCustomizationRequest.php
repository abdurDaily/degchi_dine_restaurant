<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class ThemeCustomizationRequest extends FormRequest
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
            'data-layout' => 'required|string', // Add validation rules
            'data-theme' => 'required|string',
            'data-bs-theme' => 'required|string',
            'data-sidebar-visibility' => 'required|string',
            'data-layout-width' => 'required|string',
            'data-layout-position' => 'required|string',
            'data-topbar' => 'required|string',
            'data-sidebar-size' => 'required|string',
            'data-layout-style' => 'required|string',
            'data-sidebar' => 'required|string',
            'data-sidebar-image' => 'required|string',
            'data-theme-colors' => 'required|string',
            'data-body-image' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'The :attribute field is required.',
            'in' => 'The :attribute field must be one of the allowed values.',
        ];
    }

    protected function passedValidation()
    {
        $this->replace($this->only([
            'data-layout',
            'data-theme',
            'data-bs-theme',
            'data-sidebar-visibility',
            'data-layout-width',
            'data-layout-position',
            'data-topbar',
            'data-sidebar-size',
            'data-layout-style',
            'data-sidebar',
            'data-sidebar-image',
            'data-theme-colors',
            'data-body-image',
        ]));
    }
}
