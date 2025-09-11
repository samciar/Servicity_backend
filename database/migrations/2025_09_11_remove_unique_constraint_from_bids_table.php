<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove the unique constraint
        Schema::table('bids', function (Blueprint $table) {
            $table->dropUnique(['task_id', 'tasker_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the unique constraint
        Schema::table('bids', function (Blueprint $table) {
            $table->unique(['task_id', 'tasker_id']);
        });
    }
};
