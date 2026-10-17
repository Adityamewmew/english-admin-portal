<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sidebar_menu_groups')) {
            Schema::create('sidebar_menu_groups', function (Blueprint $table) {
                $table->id();
                $table->string('key', 50)->unique();
                $table->string('label', 100);
                $table->string('color', 50)->default('blue');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('sidebar_menus')) {
            Schema::create('sidebar_menus', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('label', 255);
                $table->string('route_name', 255)->nullable();
                $table->string('icon', 255)->nullable();
                $table->string('group', 50)->default('utama');
                $table->integer('sort_order')->default(0);
                $table->smallInteger('is_active')->default(1);
                $table->string('created_by', 36)->nullable();
                $table->string('updated_by', 36)->nullable();
                $table->string('deleted_by', 36)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('sidebar_menu_accesses')) {
            Schema::create('sidebar_menu_accesses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sidebar_menu_id');
                $table->integer('access_type');
                $table->string('created_by', 36)->nullable();
                $table->timestamps();

                $table->unique(['sidebar_menu_id', 'access_type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sidebar_menu_accesses');
        Schema::dropIfExists('sidebar_menus');
        Schema::dropIfExists('sidebar_menu_groups');
    }
};
