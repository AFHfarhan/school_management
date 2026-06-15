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
            if (!Schema::hasColumn('component_table', 'code')) {
                $table->string('code')->nullable()->after('id');
            }

            if (!Schema::hasColumn('component_table', 'structure')) {
                $table->string('structure')->nullable()->after('code');
            }

            if (!Schema::hasColumn('component_table', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
        });

        Schema::table('component_table', function (Blueprint $table) {
            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('component_table', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn(['code', 'structure', 'description']);
        });
    }
};
