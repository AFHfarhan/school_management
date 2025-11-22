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
        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('level', 20)->index();
            $table->string('channel')->index();
            $table->string('message');
            $table->json('context')->nullable();
            $table->string('user_id')->nullable()->index();
            $table->string('ip')->nullable()->index();
            $table->string('path')->nullable()->index();
            $table->timestamp('logged_at')->useCurrent()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
