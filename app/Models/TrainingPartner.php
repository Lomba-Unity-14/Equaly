<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'description',
    'duration',
    'level',
    'format',
    'outcomes',
    'category',
    'type',
    'website_url',
    'accessibility',
    'is_active',
])]
class TrainingPartner extends Model
{
    protected function casts(): array
    {
        return [
            'accessibility' => 'array',
            'outcomes' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
