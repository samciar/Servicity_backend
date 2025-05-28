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
        Schema::create('tasker_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tasker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->string('proficiency_level', 50)->nullable();
            $table->timestamps();

            // Add unique composite index
            $table->unique(['tasker_id', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasker_skills');
    }
};
