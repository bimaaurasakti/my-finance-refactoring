<?php

namespace App\Http\Requests;

class RegisterUserRequest extends BaseFormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'username.required' => 'Anda harus mengisi username!',
            'email.required' => 'Anda harus mengisi email!',
            'password.required' => 'Anda harus mengisi password!',
        ];
    }
}
