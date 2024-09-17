<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get users related to this project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('project_id', 'user_id', 'role', 'contribution_hours', 'last_activity')
            ->withTimestamps();
    }

    /**
     * Get tasks related to this project
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assign_to_project');
    }

    /**
     * Get latest task assigned to specifeid project
     * @return Model|object|\Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function latestTask()
    {
        return $this->hasOne(Task::class, 'assign_to_project')->latestOfMany()->first();
    }

    /**
     * Get oldest task assigned to specifeid project
     * @return Model|object|\Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function oldestTask()
    {
        return $this->hasOne(Task::class, 'assign_to_project')->oldestOfMany()->first();
    }

    /**
     * Get high priority tasks assigned to specifeid project
     * @return mixed
     */
    public function highPriorityTask()
    {
        return $this->tasks()
            ->where('priority', 'high')
            ->ofMany('created_at', 'latest');
    }
}
