<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'url',
    'company',
    'location',
    'job_title',
    'jobdesk',
    'salary',
    'job_category',
    'skill_req',
    'education_req',
    'work_type',
    'company_size',
    'image_logo_url',
])]
class JobVacancyData extends Model
{
    public function matches(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JobUserMatch::class);
    }
}
