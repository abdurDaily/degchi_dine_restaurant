<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class EmailSettingRequest extends FormRequest
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
    public function rules()
    {
        $rules = [
            'mail_driver' => 'required|string|max:255',
            'mail_password' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ];

        switch ($this->input('email_setting')) {
            case 'custom':
            case 'smtp':
                $rules = array_merge($rules, [
                    'mail_host' => 'required|string|max:255',
                    'mail_port' => 'required|integer',
                    'mail_encryption' => 'required|string|max:10',
                ]);
                break;

            case 'gmail':
                $rules = array_merge($rules, [
                    'mail_host' => 'in:smtp.gmail.com',
                    'mail_port' => 'in:587',
                    'mail_encryption' => 'in:TLS',
                ]);
                break;

            case 'outlook':
                $rules = array_merge($rules, [
                    'mail_host' => 'in:smtp.office365.com',
                    'mail_port' => 'in:587',
                    'mail_encryption' => 'in:TLS',
                ]);
                break;

            case 'yahoo':
                $rules = array_merge($rules, [
                    'mail_host' => 'in:smtp.mail.yahoo.com',
                    'mail_port' => 'in:587',
                    'mail_encryption' => 'in:TLS',
                ]);
                break;

            case 'sendgrid':
                $rules = array_merge($rules, [
                    'mail_host' => 'in:smtp.sendgrid.net',
                    'mail_port' => 'in:587',
                    'mail_encryption' => 'in:TLS',
                ]);
                break;

            case 'amazon':
                $rules = array_merge($rules, [
                    'mail_host' => 'in:email-smtp.us-east-1.amazonaws.com',
                    'mail_port' => 'in:587',
                    'mail_encryption' => 'in:TLS',
                ]);
                break;

            case 'mailgun':
                $rules = array_merge($rules, [
                    'mail_host' => 'in:smtp.mailgun.org',
                    'mail_port' => 'in:587',
                    'mail_encryption' => 'in:TLS',
                ]);
                break;

            case 'smtp.com':
                $rules = array_merge($rules, [
                    'mail_host' => 'in:smtp.smtp.com',
                    'mail_port' => 'in:587',
                    'mail_encryption' => 'in:TLS',
                ]);
                break;

            case 'zohomail':
                $rules = array_merge($rules, [
                    'mail_host' => 'in:smtp.zoho.com',
                    'mail_port' => 'in:587',
                    'mail_encryption' => 'in:TLS',
                ]);
                break;

            case 'mailtrap':
                $rules = array_merge($rules, [
                    'mail_host' => 'in:smtp.mailtrap.io',
                    'mail_port' => 'in:587',
                    'mail_encryption' => 'in:TLS',
                ]);
                break;

            case 'mandrill':
                $rules = array_merge($rules, [
                    'mail_host' => 'in:smtp.mandrillapp.com',
                    'mail_port' => 'in:587',
                    'mail_encryption' => 'in:TLS',
                ]);
                break;

            case 'sparkpost':
                $rules = array_merge($rules, [
                    'mail_host' => 'in:smtp.sparkpostmail.com',
                    'mail_port' => 'in:587',
                    'mail_encryption' => 'in:TLS',
                ]);
                break;

            default:
                break;
        }

        return $rules;
    }

    protected function passedValidation()
    {
        $this->replace($this->only([
            'email',
            'mail_driver',
            'mail_host',
            'mail_port',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
            'email_setting',
        ]));
    }

}
