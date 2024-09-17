<?php

namespace App\Http\Requests\Task;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreFormRequest extends FormRequest
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

        return Auth::user()->is_admin == true || $hasRole;
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
            'title'             =>      'required|string|min:2|max:100',
            'description'       =>      'required|string',
            'priority'          =>      'required|string|in:low,middle,high',
            'assign_to_project' =>      'required|numeric|min:1',
            'assign_to_user'    =>      'required|numeric|min:1',
            'role'              =>      'required|string|in:manager,developer,tester',
            'due_date'          =>      'required|date_format:d-m-Y H:i'
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
            'title'             => 'Task title',
            'description'       => 'Task description',
            'priority'          => 'Task priority',
            'assign_to_project' => 'Assignee task to project',
            'assign_to_user'    => 'Assignee task to user',
            'role'              => 'User role',
            'due_date'          => 'Due date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return array
     */
    public function messages(): array
    {
        return [
            'required'       => 'The :attribute field is required.',
            'min'            => 'The :attribute field must be at least :min.',
            'max'            => 'The :attribute field must not be greater than :max.',
            'date_format'    => 'Please provide a valid date format for the :attribute. Expected format: d-m-Y H:i.',
        ];
    }
}
