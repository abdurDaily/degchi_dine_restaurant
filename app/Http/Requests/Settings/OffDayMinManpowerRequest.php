<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class OffDayMinManpowerRequest extends FormRequest
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
            'offday_minimum_bus_manpower' => 'required',
            'offday_minimum_dinning_manpower' => 'required',
        ];
    }

    protected function passedValidation()
    {
        $this->replace($this->only([
            'offday_minimum_bus_manpower',
            'offday_minimum_dinning_manpower',
        ]));
    }
}
