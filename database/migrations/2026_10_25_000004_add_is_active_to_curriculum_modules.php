<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('curriculum_modules') && ! Schema::hasColumn('curriculum_modules', 'is_active')) {
            Schema::table('curriculum_modules', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('is_exam');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('curriculum_modules') && Schema::hasColumn('curriculum_modules', 'is_active')) {
            Schema::table('curriculum_modules', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
