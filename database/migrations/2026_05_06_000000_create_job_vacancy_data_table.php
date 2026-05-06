<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_vacancy_data', function (Blueprint $table) {
            $table->id();
            $table->text('url')->nullable();
            $table->string('company')->nullable();
            $table->string('location')->nullable();
            $table->string('job_title')->nullable();
            $table->longText('jobdesk')->nullable();
            $table->string('salary')->nullable();
            $table->string('job_category')->nullable();
            $table->string('skill_req')->nullable();
            $table->string('education_req')->nullable();
            $table->string('work_type')->nullable();
            $table->string('company_size')->nullable();
            $table->text('image_logo_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_vacancy_data');
    }
};
