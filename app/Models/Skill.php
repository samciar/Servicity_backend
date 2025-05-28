<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the category that owns the skill.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the users (taskers) that have this skill.
     */
    public function taskers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tasker_skills')
                    ->withPivot('proficiency_level')
                    ->withTimestamps()
                    ->where('user_type', 'tasker'); // Only users with tasker role
    }

    /**
     * Get tasks that require this skill.
     */
    public function tasks()
    {
        return $this->hasManyThrough(
            Task::class,
            TaskSkill::class, // Pivot table if you have one
            'skill_id',
            'id',
            'id',
            'task_id'
        );
    }

    /**
     * Scope a query to search skills by name.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Scope a query to only include skills from a specific category.
     */
    public function scopeFromCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to include skills with taskers.
     */
    public function scopeWithTaskers($query)
    {
        return $query->whereHas('taskers');
    }

    /**
     * Get the average proficiency level for this skill across all taskers.
     */
    public function averageProficiency()
    {
        return $this->taskers()
            ->average('tasker_skills.proficiency_level');
    }
}