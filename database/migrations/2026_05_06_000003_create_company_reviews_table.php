<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('job_application_id')->unique()->constrained('job_applications')->cascadeOnDelete();
            $table->string('company_name')->nullable()->index();
            $table->boolean('is_accepted')->default(false);
            $table->unsignedTinyInteger('has_disability_employees')->default(0)->comment('0=No, 1=Yes, 2=NotSure');
            $table->boolean('is_friendly')->default(false);
            $table->text('experience')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_reviews');
    }
};
