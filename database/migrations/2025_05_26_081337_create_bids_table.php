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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('tasker_id')->constrained('users')->onDelete('cascade');
            $table->decimal('bid_amount', 12, 2);
            $table->text('message')->nullable();
            $table->string('status', 50)->default('pending')->comment("'pending', 'accepted', 'rejected', 'withdrawn'");
            $table->timestamps();

            // Composite unique constraint
            $table->unique(['task_id', 'tasker_id']);
            
        });
        
        // Add check constraint for status
        DB::statement("
            ALTER TABLE bids 
            ADD CONSTRAINT bids_status_check 
            CHECK (status IN ('pending', 'accepted', 'rejected', 'withdrawn'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE bids DROP CONSTRAINT IF EXISTS bids_status_check");

        Schema::dropIfExists('bids');
    }
};
