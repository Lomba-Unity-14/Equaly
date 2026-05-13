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
            $table->text('job_url')->nullable();
            $table->string('company')->nullable();
            $table->string('location')->nullable();
            $table->string('job_title')->nullable();
            $table->longText('job_detail')->nullable();
            $table->string('salary')->nullable();
            $table->string('category')->nullable();
            $table->string('skill_req')->nullable();
            $table->string('education_req')->nullable();
            $table->string('work_type')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('company_size')->nullable();
            $table->text('company_logo_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_vacancy_data');
    }
};
