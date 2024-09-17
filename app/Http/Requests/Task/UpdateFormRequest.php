<?php

namespace App\Http\Requests\Task;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateFormRequest extends FormRequest
{
    use ResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $projectId = $this->route('project');

        $hasRole = DB::table('project_user')
            ->where('user_id', Auth::id())
            ->where('project_id', $projectId)
            ->where('role', 'manager')
            ->exists();
        return Auth::user()->is_admin === true || $hasRole;
    }

    /**
     * Get errors that show from authorize
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    public function failedAuthorization()
    {
        throw new HttpResponseException($this->getResponse('error', 'This action is unauthorized.', 401));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'             =>      'nullable|string|max:100',
            'description'       =>      'nullable|string',
            'priority'          =>      'required|string|in:low,middle,high',
            'due_date'          =>      'nullable|date_format:d-m-Y H:i'
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
     * Get custom attributes for validator errors.
     * @return array
     */
    public function attributes(): array
    {
        return [
            'title'        => 'Task title',
            'description'  => 'Task description',
            'priority'     => 'Task priority',
            'due_date'     => 'Due date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return array
     */
    public function messages(): array
    {
        return [
            'numeric'        => 'The :attribute must be a number.',
            'max'            => 'The :attribute field must not be greater than :max.',
            'date_format'    => 'Please provide a valid date format for the :attribute. Expected format: d-m-Y H:i.',
        ];
    }
}
