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
        Schema::table('component_table', function (Blueprint $table) {
            if (!Schema::hasColumn('component_table', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('category');
            }

            if (!Schema::hasColumn('component_table', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('component_table', function (Blueprint $table) {
            if (Schema::hasColumn('component_table', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('component_table', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
