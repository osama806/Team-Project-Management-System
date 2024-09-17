<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'assign_to_project',
        'assign_to_user',
        'due_date',
        'notes'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get user related to task
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assign_to_user');
    }

    /**
     * Get project related to task
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'assign_to_project');
    }


    /**
     * Filter tasks based on status, priority, and optionally related models.
     *
     * @param string|null $status
     * @param string|null $priority
     * @param string|null $user_id
     * @param string|null $project_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filterTask($status = null, $priority = null, $user_id = null, $project_id = null)
    {
        return Task::query()
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($priority, function ($query) use ($priority) {
                $query->where('priority', $priority);
            })
            ->when($user_id, function ($query) use ($user_id) {
                $query->whereRelation('user', 'id', $user_id);
            })
            ->when($project_id, function ($query) use ($project_id) {
                $query->whereRelation('project', 'id', $project_id);
            });
    }
}
