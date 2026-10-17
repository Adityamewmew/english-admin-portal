<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->string('id', 36)->primary();
                $table->string('name', 100);
                $table->string('email', 100)->unique();
                $table->string('password', 255);
                $table->string('role', 20)->default('student');
                $table->integer('access_type')->default(2);
                $table->string('current_cefr', 10)->default('A1');
                $table->string('avatar_url', 255)->nullable();
                $table->json('memory')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->string('created_by', 36)->nullable();
                $table->string('updated_by', 36)->nullable();
                $table->string('deleted_by', 36)->nullable();
            });
        }

        if (! Schema::hasTable('curriculum_levels')) {
            Schema::create('curriculum_levels', function (Blueprint $table) {
                $table->string('id', 20)->primary();
                $table->string('cefr', 10);
                $table->string('title', 150);
                $table->text('description')->nullable();
                $table->integer('order_index')->default(1);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('curriculum_modules')) {
            Schema::create('curriculum_modules', function (Blueprint $table) {
                $table->string('id', 20)->primary();
                $table->string('level_id', 20)->default('A1.1');
                $table->string('title', 150);
                $table->string('cefr', 10);
                $table->string('group', 100);
                $table->text('objective');
                $table->string('complexity', 20)->default('medium');
                $table->integer('estimated_minutes')->default(15);
                $table->boolean('is_exam')->default(false);
                $table->integer('passing_score')->default(70);
                $table->integer('order_index')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('module_sections')) {
            Schema::create('module_sections', function (Blueprint $table) {
                $table->string('id', 36)->primary();
                $table->string('module_id', 20);
                $table->string('sub_module_id', 36)->nullable();
                $table->string('section_type', 30);
                $table->string('title', 150);
                $table->json('content');
                $table->integer('order_index')->default(1);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('sub_modules')) {
            Schema::create('sub_modules', function (Blueprint $table) {
                $table->string('id', 36)->primary();
                $table->string('module_id', 20);
                $table->string('skill_type', 20);
                $table->string('title', 150);
                $table->integer('order_index')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('user_module_progress')) {
            Schema::create('user_module_progress', function (Blueprint $table) {
                $table->string('id', 36)->primary();
                $table->string('user_id', 36);
                $table->string('module_id', 20);
                $table->string('status', 20)->default('locked');
                $table->integer('score')->nullable();
                $table->json('step_progress')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('item_bank')) {
            Schema::create('item_bank', function (Blueprint $table) {
                $table->string('id', 50)->primary();
                $table->string('skill', 20);
                $table->string('cefr', 10);
                $table->string('type', 20)->default('MCQ');
                $table->text('question');
                $table->json('options')->nullable();
                $table->text('answer');
                $table->text('audio_script')->nullable();
                $table->string('audio_url', 255)->nullable();
                $table->text('explanation')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('item_bank');
        Schema::dropIfExists('user_module_progress');
        Schema::dropIfExists('sub_modules');
        Schema::dropIfExists('module_sections');
        Schema::dropIfExists('curriculum_modules');
        Schema::dropIfExists('curriculum_levels');
        Schema::dropIfExists('users');
    }
};
