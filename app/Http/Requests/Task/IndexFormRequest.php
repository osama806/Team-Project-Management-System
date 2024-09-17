<?php

namespace App\Http\Requests\Task;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class IndexFormRequest extends FormRequest
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
            'status'        =>      'nullable|string|in:in-progress,done',
            'priority'      =>      'nullable|string|in:low,middle,high',
            'project_id'    =>      'nullable|numeric|min:1',
            'user_id'       =>      'nullable|numeric|min:1',
        ];
    }

    /**
     * Get message that errors explanation
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException($this->getResponse('errors', $validator->errors(), 422));
    }

    /**
     * Get custom attributes for validator errors
     * @return string[]
     */
    public function attributes()
    {
        return [
            'status'        =>  'Task status',
            'priority'      =>  'Task priority',
            'project_id'    =>  'Project number',
            'user_id'       =>  'User number',
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return string[]
     */
    public function messages()
    {
        return [
            'status.in' => 'The status must be either in-progress or done.',
            'priority.in' => 'The priority must be one of the following: low, middle, or high.',
            'numeric'        => 'The :attribute must be a number.',
            'min'            => 'The :attribute field must be at least :min.',
        ];
    }
}
