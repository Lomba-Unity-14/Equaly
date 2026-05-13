<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'disability_condition',
    'hearing_level',
    'communication_preference',
    'work_environment',
    'skill_categories',
    'education_level',
    'education_major',
    'job_types',
    'preferred_locations',
    'onboarding_completed',
    'headline',
])]
class UserProfile extends Model
{
    protected function casts(): array
    {
        return [
            'disability_condition' => 'array',
            'communication_preference' => 'array',
            'work_environment' => 'array',
            'skill_categories' => 'array',
            'job_types' => 'array',
            'preferred_locations' => 'array',
            'onboarding_completed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function matches(): HasMany
    {
        return $this->hasManyThrough(JobUserMatch::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }
}
