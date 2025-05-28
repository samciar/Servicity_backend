<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'icon_url',
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
     * Get the skills associated with this category.
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    /**
     * Get the tasks associated with this category.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Scope a query to search categories by name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Get the URL for the category's icon.
     * Returns a default icon if none is set.
     */
    public function getIconUrlAttribute($value): string
    {
        return $value ?? asset('images/default-category-icon.png');
    }

    /**
     * Get only the categories that have skills.
     */
    public function scopeWithSkills($query)
    {
        return $query->whereHas('skills');
    }

    /**
     * Get only the categories that have active tasks.
     */
    public function scopeWithActiveTasks($query)
    {
        return $query->whereHas('tasks', function ($query) {
            $query->whereIn('status', ['open', 'assigned', 'in_progress']);
        });
    }
}