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
            'company' => 'nullable|string|max:250',
            'tagline' => 'nullable|string|max:250',
            'currency' => 'required|exists:currencies,id',
            'delivery_charge' => 'nullable|numeric|min:0|max:999999.99',
            'delivery_per_km_charge' => 'nullable|numeric|min:0|max:999999.99',
            'delivery_included_km' => 'nullable|numeric|min:0|max:1000',
        ];
    }

    protected function passedValidation()
    {
        $data = $this->only([
            'company',
            'tagline',
            'currency',
            'delivery_charge',
            'delivery_per_km_charge',
            'delivery_included_km',
        ]);

        // A blank delivery field means "leave the saved/default value alone",
        // so it must not overwrite a previously configured amount with ''.
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'delivery_') && ($value === null || $value === '')) {
                unset($data[$key]);
            }
        }

        // Company and tagline are optional, but the settings.value column is
        // NOT NULL. Normalize an empty submission to '' so it can be saved.
        foreach (['company', 'tagline'] as $key) {
            if (array_key_exists($key, $data) && $data[$key] === null) {
                $data[$key] = '';
            }
        }

        $this->replace($data);
    }
}
