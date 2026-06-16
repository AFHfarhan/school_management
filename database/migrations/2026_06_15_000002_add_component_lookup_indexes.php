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
            $table->index(['category', 'name']);
            $table->index(['code', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('component_table', function (Blueprint $table) {
            $table->dropIndex(['category', 'name']);
            $table->dropIndex(['code', 'category']);
        });
    }
};
