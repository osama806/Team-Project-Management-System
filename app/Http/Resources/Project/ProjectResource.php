<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\Task\TaskResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the project into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'              =>      $this->name,
            'description'       =>      $this->description,
            'tasks'             =>      TaskResource::collection($this->tasks)
        ];
    }
}
