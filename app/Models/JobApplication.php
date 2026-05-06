<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id',
    'job_vacancy_data_id',
    'company_name',
    'applied_at',
])]
class JobApplication extends Model
{
    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobVacancyData(): BelongsTo
    {
        return $this->belongsTo(JobVacancyData::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(CompanyReview::class);
    }
}
