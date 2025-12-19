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
        Schema::create('teacher_attendance', function (Blueprint $table) {
            $table->id();
            $table->string('teacher_name');
            $table->date('attendance_date');
            $table->string('tahun_ajaran');
            $table->string('semester');
            $table->json('data'); // Stores: { "schedule": [...], "recorded_at": "...", "recorded_by": "..." }
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['teacher_name', 'attendance_date']);
            $table->index(['tahun_ajaran', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_attendance');
    }
};
