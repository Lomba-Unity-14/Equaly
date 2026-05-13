<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_user_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_vacancy_data_id')->constrained('job_vacancy_data')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('match_score')->default(0);
            $table->boolean('is_match')->default(false);
            $table->unsignedTinyInteger('disability_score')->default(0);
            $table->unsignedTinyInteger('skill_score')->default(0);
            $table->unsignedTinyInteger('environment_score')->default(0);
            $table->unsignedTinyInteger('communication_score')->default(0);
            $table->unsignedTinyInteger('education_score')->default(0);
            $table->text('match_reason')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['job_vacancy_data_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_user_matches');
    }
};
