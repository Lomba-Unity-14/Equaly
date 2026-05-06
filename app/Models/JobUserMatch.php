<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'job_vacancy_data_id',
    'user_id',
    'match_score',
    'is_match',
    'disability_score',
    'skill_score',
    'environment_score',
    'communication_score',
    'education_score',
    'match_reason',
    'calculated_at',
])]
class JobUserMatch extends Model
{
    public function jobVacancyData(): BelongsTo
    {
        return $this->belongsTo(JobVacancyData::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
