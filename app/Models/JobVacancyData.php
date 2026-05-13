<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'job_url',
    'company',
    'location',
    'job_title',
    'job_detail',
    'salary',
    'category',
    'skill_req',
    'education_req',
    'work_type',
    'employment_type',
    'company_size',
    'company_logo_url',
])]
class JobVacancyData extends Model
{
    public function matches(): HasMany
    {
        return $this->hasMany(JobUserMatch::class);
    }
}
