<?php

namespace App\Services\Task;

use App\Http\Resources\Task\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    use ResponseTrait;

    /**
     * Get list of tasks
     * @return array
     */
    public function index(array $data)
    {
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        (count($filteredData) > 0)
            ?  $tasks = (new Task())->filterTask(
                $filteredData['status'] ?? null,
                $filteredData['priority'] ?? null,
                $filteredData['user_id'] ?? null,
                $filteredData['project_id'] ?? null
            )->get()
            : $tasks = Task::all();

        if ($tasks->isEmpty()) {
            return [
                'status'     => false,
                'msg'        => 'Not Found Any Task!',
                'code'       => 404
            ];
        }

        return [
            'status' => true,
            'tasks' => TaskResource::collection($tasks)
        ];
    }

    /**
     * Create new task in storage
     * @param array $data
     * @return array
     */
    public function createTask(array $data)
    {
        $user = User::where('id', $data['assign_to_user'])->where('is_admin', false)->first();
        if (!$user) {
            return [
                'status'            =>      false,
                'msg'               =>      "Can't assign to this user",
                'code'              =>      400
            ];
        }

        $project = Project::find($data['assign_to_project']);
        if (!$project) {
            return [
                'status'            =>      false,
                'msg'               =>      'Not Found This Project',
                'code'              =>      404
            ];
        }

        try {
            // date with timezone
            $dueDate = Carbon::createFromFormat('d-m-Y H:i', $data['due_date']);
            if ($dueDate->isPast()) {
                return ['status' => false, 'msg' => 'Due date must be a future date.', 'code' => 400];
            }
        } catch (\InvalidArgumentException $e) {
            return ['status' => false, 'msg' => 'Invalid due date format, please use d-m-Y H:i', 'code' => 400];
        }

        $task = Task::create([
            'title'             => $data['title'],
            'description'       => $data['description'],
            'priority'          => $data['priority'],
            'assign_to_project' => $project->id,
            'assign_to_user'    => $user->id,
            'due_date'          => $dueDate->toDateTimeString()
        ]);

        $project->users()->attach($user->id, [
            'role'                  =>   $data['role'],
            'contribution_hours'    =>   0,
            'last_activity'         =>   now()
        ]);

        return $task
            ? ['status'    =>  true]
            : ['status'    =>  false, 'msg'    =>  'There is error in server', 'code'  =>  500];
    }

    /**
     * Get spicified task
     * @param \App\Models\Task $task
     * @return array
     */
    public function show(Task $task)
    {
        return [
            'status'    =>  true,
            'task'      =>  new TaskResource($task)
        ];
    }

    /**
     * Update a spicified task details in storage
     * @param array $data
     * @param \App\Models\Task $task
     * @return array
     */
    public function update(array $data, Task $task)
    {
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        if (isset($filteredData['due_date'])) {
            // Convert the due_date to the proper MySQL format
            $date = DateTime::createFromFormat('d-m-Y H:i', $filteredData['due_date']);
            if ($date) {
                // Format the date as Y-m-d H:i:s
                $filteredData['due_date'] = $date->format('Y-m-d H:i:s');
            } else {
                return [
                    'status' => false,
                    'msg' => 'Invalid date format',
                    'code' => 400
                ];
            }
        }

        if (count($filteredData) < 1) {
            return [
                'status' => false,
                'msg' => 'Not Found Any Data in Request',
                'code' => 404
            ];
        }

        $task->update($filteredData);
        return ['status' => true];
    }

    /**
     * Remove a specified task from storage
     * @param \App\Models\Task $task
     * @return bool[]|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(Task $task)
    {
        if (Auth::user()->is_admin == false) {
            return ['status'    =>  false,  'msg' => "Can't access delete permission", 'code' => 400];
        }
        $task->delete();
        return ['status'    =>  true];
    }

    /**
     * Retrive task after deleted
     * @param \App\Models\Task $task
     * @return array
     */
    public function restore(Task $task)
    {
        if (Auth::user()->is_admin == false) {
            return [
                'status'        =>      false,
                'msg'           =>      "Can't access delete permission",
                'code'          =>       400
            ];
        }
        if ($task->deleted_at === null) {
            return [
                'status' => false,
                'msg' => "This task isn't deleted",
                'code' => 400,
            ];
        }
        $task->restore();

        return ['status' => true];
    }

    /**
     * Deliveried task to admin
     * @param \App\Models\Task $task
     * @return array
     */
    public function delivery(Task $task)
    {
        if (Auth::user()->is_admin == true || $task->status !== 'in-progress') {
            return ['status'    =>  false,  'msg' => 'User unAuthorization or task status not in-progress', 'code'  =>   400];
        }
        if ($task->assign_to_user !== Auth::id()) {
            return ['status'    =>  false,  'msg' => 'This task assigned to another user', 'code'  =>   400];
        }
        $task->status = 'done';
        $task->due_date = now()->toDateTimeString();
        $task->save();
        return ['status'    =>  true];
    }
}
