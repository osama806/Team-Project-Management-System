<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreFormRequest;
use App\Http\Requests\Project\UpdateFormRequest;
use App\Models\Project;
use App\Services\Project\ProjectService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ResponseTrait;
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of the projects
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        $response = $this->projectService->index();
        return $response['status']
            ? $this->getResponse('projects', $response['projects'], 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Store a newly created project in storage
     * @param \App\Http\Requests\Project\StoreFormRequest $storeFormRequest
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(StoreFormRequest $storeFormRequest)
    {
        $validated = $storeFormRequest->validated();
        $response = $this->projectService->store($validated);
        return $response['status']
            ? $this->getResponse('msg', 'Created project successfully', 201)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Display the specified project
     * @param string $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return $this->getResponse('error', 'Not Found This Project', 404);
        }
        $response = $this->projectService->show($project);
        return $response['status']
            ? $this->getResponse('project', $response['project'], 200)
            : $this->getResponse('error', "There is error in server", 500);
    }

    /**
     * Update the specified project in storage
     * @param \App\Http\Requests\Project\UpdateFormRequest $updateFormRequest
     * @param string $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(UpdateFormRequest $updateFormRequest, string $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return $this->getResponse('error', 'Not Found This Project', 404);
        }
        $validated = $updateFormRequest->validated();
        $response = $this->projectService->update($validated, $project);
        return $response['status']
            ? $this->getResponse('msg', 'Updated project susccessfully', 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Remove the specified project from storage
     * @param string $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return $this->getResponse('error', 'Not Found This Project', 404);
        }
        $response = $this->projectService->deleteProject($project);
        return $response['status']
            ? $this->getResponse('msg', 'Deleted project successfullt', 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Retrive user after deleted
     * @param string $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function restore(string $id)
    {
        $project = Project::onlyTrashed()->find($id);
        if (!$project) {
            return $this->getResponse('error', 'Project not found or not soft-deleted', 404);
        }
        $response = $this->projectService->restore($project);
        return $response['status']
            ? $this->getResponse('msg', 'Project restored successfully', 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Get latest task in specified project
     * @param string $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function latestTask(string $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return $this->getResponse('error', 'Project Not Found', 404);
        }
        $response = $this->projectService->latest($project);
        return $response['status']
            ? $this->getResponse('task', $response['task'], 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Get oldest task in specified project
     * @param string $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function oldestTask(string $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return $this->getResponse('error', 'Project Not Found', 404);
        }
        $response = $this->projectService->oldest($project);
        return $response['status']
            ? $this->getResponse('task', $response['task'], 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Get task that have high priority in specified project
     * @param string $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function highPriority(string $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return $this->getResponse('error', 'Project Not Found', 404);
        }
        $response = $this->projectService->priority($project);
        return $response['status']
            ? $this->getResponse('tasks', $response['tasks'], 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }
}
