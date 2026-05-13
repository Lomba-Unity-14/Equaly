<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_partners', function (Blueprint $table) {
            $table->string('duration')->nullable()->after('description');
            $table->string('level')->nullable()->after('duration');
            $table->string('format')->nullable()->after('level');
            $table->json('outcomes')->nullable()->after('format');
        });
    }

    public function down(): void
    {
        Schema::table('training_partners', function (Blueprint $table) {
            $table->dropColumn(['duration', 'level', 'format', 'outcomes']);
        });
    }
};
