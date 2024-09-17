<?php

namespace App\Services\Project;

use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Task\TaskResource;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    /**
     * Display a listing of the projects
     * @return array
     */
    public function index()
    {
        $projects = Project::with('tasks')->get();
        if (count($projects) < 1) {
            return [
                'status'        =>      false,
                'msg'           =>      'Not Found Any Project',
                'code'          =>      404
            ];
        }
        return [
            'status'        =>      true,
            'projects'      =>      ProjectResource::collection($projects)
        ];
    }

    /**
     * Store a newly created project in storage
     * @param array $data
     * @return array
     */
    public function store(array $data)
    {
        try {
            Project::create([
                'name'              =>      $data['name'],
                'description'       =>      $data['description']
            ]);
            return ['status' => true];
        } catch (\Throwable $th) {
            return [
                'status'        =>      false,
                'msg'           =>      "Can't create new project. Try again",
                'code'          =>      500
            ];
        }
    }

    /**
     * Display the specified project
     * @param \App\Models\Project $project
     * @return array
     */
    public function show(Project $project)
    {
        return [
            'status'        =>      true,
            'project'       =>      new ProjectResource($project)
        ];
    }

    /**
     * Update the specified project in storage
     * @param array $data
     * @param \App\Models\Project $project
     * @return array
     */
    public function update(array $data, Project $project)
    {
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && trim($value) !== '';
        });
        if (count($filteredData) < 1) {
            return [
                'status'            =>      false,
                'msg'               =>      'Not Found Any Data in Request',
                'code'              =>      404
            ];
        }
        $project->update($filteredData);
        return  ['status'   =>  true];
    }

    /**
     * Remove the specified project from storage
     * @param \App\Models\Project $project
     * @return array
     */
    public function deleteProject(Project $project)
    {
        $hasRole = DB::table('project_user')
            ->where('user_id', Auth::id())
            ->where('project_id', $project->id)
            ->where('role', 'manager')
            ->exists();
        if (Auth::user()->is_admin == false || !$hasRole) {
            return [
                'status'    =>  false,
                'msg'       =>  "You don't have permission to delete this project.",
                'code'      =>  400
            ];
        }
        $project->delete();
        return ['status'    =>  true];
    }

    /**
     * Retrive user after deleted
     * @param \App\Models\Project $project
     * @return array
     */
    public function restore(Project $project)
    {
        $hasRole = DB::table('project_user')
            ->where('user_id', Auth::id())
            ->where('project_id', $project->id)
            ->where('role', 'manager')
            ->exists();
        if (Auth::user()->is_admin == false || !$hasRole) {
            return [
                'status'    =>  false,
                'msg'       =>  "You don't have permission to restore this project.",
                'code'      =>  400
            ];
        }
        if ($project->deleted_at === null) {
            return [
                'status' => false,
                'msg' => "This project isn't deleted",
                'code' => 400,
            ];
        }
        $project->restore();

        return ['status' => true];
    }

    /**
     * Get latest task in specified project
     * @param \App\Models\Project $project
     * @return array
     */
    public function latest(Project $project)
    {
        if ($project->tasks()->count() < 1) {
            return [
                'status'        => false,
                'msg'           => 'Not Found Any Task in This Project',
                'code'          => 404
            ];
        }

        $task = $project->latestTask();

        return [
            'status'            => true,
            'task'              => new TaskResource($task) // TaskResource should expect a Task model
        ];
    }

    /**
     * Get oldest task in specified project
     * @param \App\Models\Project $project
     * @return array
     */
    public function oldest(Project $project)
    {
        if ($project->tasks()->count() < 1) {
            return [
                'status'        => false,
                'msg'           => 'Not Found Any Task in This Project',
                'code'          => 404
            ];
        }

        $task = $project->oldestTask();

        return [
            'status'            => true,
            'task'              => new TaskResource($task) // TaskResource should expect a Task model
        ];
    }

    /**
     * Get task that have high priority in specified project
     * @param \App\Models\Project $project
     * @return array
     */
    public function priority(Project $project)
    {
        if ($project->tasks()->count() < 1) {
            return [
                'status'        => false,
                'msg'           => 'Not Found Any Task in This Project',
                'code'          => 404
            ];
        }

        $tasks = $project->highPriorityTask();

        if ($tasks->count() < 1) {
            return [
                'status'        =>      false,
                'msg'           =>      'Not Found Tasks That High Priority!',
                'code'          =>      404
            ];
        }

        return [
            'status'            => true,
            'tasks'             => TaskResource::collection($tasks)
        ];
    }
}
