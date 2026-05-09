<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('hearing_level')->nullable()->after('disability_condition');
            $table->string('education_level')->nullable()->after('work_environment');
            $table->string('education_major')->nullable()->after('education_level');
            $table->json('job_types')->nullable()->after('education_major');
            $table->json('preferred_locations')->nullable()->after('job_types');
            $table->json('skill_categories')->nullable()->after('preferred_locations');
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('skills');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'hearing_level',
                'education_level',
                'education_major',
                'job_types',
                'preferred_locations',
                'skill_categories',
            ]);
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->json('skills')->nullable()->after('work_environment');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('date_of_birth');
        });
    }
};
