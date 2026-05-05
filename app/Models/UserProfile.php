<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'disability_condition',
    'communication_preference',
    'work_environment',
    'skills',
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
            'skills' => 'array',
            'onboarding_completed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
