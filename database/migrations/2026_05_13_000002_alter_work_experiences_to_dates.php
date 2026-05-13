<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_experiences', function (Blueprint $table) {
            if (Schema::hasColumn('work_experiences', 'start_year')) {
                $table->dropColumn(['start_year', 'end_year']);
            }

            if (! Schema::hasColumn('work_experiences', 'start_date')) {
                $table->date('start_date')->nullable()->after('position');
            }

            if (! Schema::hasColumn('work_experiences', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_experiences', function (Blueprint $table) {
            if (Schema::hasColumn('work_experiences', 'start_date')) {
                $table->dropColumn(['start_date', 'end_date']);
            }

            if (! Schema::hasColumn('work_experiences', 'start_year')) {
                $table->smallInteger('start_year')->after('position');
                $table->smallInteger('end_year')->nullable()->after('start_year');
            }
        });
    }
};
