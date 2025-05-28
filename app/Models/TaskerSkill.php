<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskerSkill extends Pivot
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'proficiency_level',
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
     * Proficiency level constants
     */
    public const LEVEL_BEGINNER = 'beginner';
    public const LEVEL_INTERMEDIATE = 'intermediate';
    public const LEVEL_EXPERT = 'expert';

    /**
     * Get available proficiency levels
     */
    public static function proficiencyLevels(): array
    {
        return [
            self::LEVEL_BEGINNER => 'Beginner',
            self::LEVEL_INTERMEDIATE => 'Intermediate',
            self::LEVEL_EXPERT => 'Expert',
        ];
    }
}