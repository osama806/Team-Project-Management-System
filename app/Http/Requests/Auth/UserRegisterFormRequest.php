<?php

namespace App\Http\Requests\Auth;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRegisterFormRequest extends FormRequest
{
    use ResponseTrait;
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
            'name'          =>      'required|string|min:3|max:100',
            'email'         =>      'required|email',
            'password'      =>      'required|confirmed|min:8|max:15',
        ];
    }

    /**
     * Get exception for error inputs
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException($this->getResponse('errors', $validator->errors(), 422));
    }

    /**
     * Get custom attributes for validator errors.
     * @return string[]
     */
    public function attributes()
    {
        return [
            'name'      =>      'Full name',
            'email'     =>      'Email address',
        ];
    }

    /**
     * Get custom error messages for validation rules
     * @return string[]
     */
    public function messages()
    {
        return [
            'required'         => 'The :attribute field is required.',
            'string'           => 'The :attribute must be a valid string.',
            'max'              => 'The :attribute may not be greater than :max characters.',
            'min'              => 'The :attribute must be at least :min characters.',
            'email'            => 'The :attribute must be a format email.',
            'confirmed'        => 'The :attribute confirmation does not match.',
        ];
    }
}
