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
            if (! Schema::hasIndex('component_table', ['is_active', 'sort_order'])) {
                $table->index(['is_active', 'sort_order']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('component_table', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'sort_order']);
        });
    }
};
