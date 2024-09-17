<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the task into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title'                 => $this->title,
            'description'           => $this->description,
            'priority'              => $this->priority,
            'status'                => $this->status,
            'related to user'       => $this->assign_to_user,
            'related to project'    => $this->assign_to_project,
            'notes'                 => $this->notes,
        ];
    }
}
