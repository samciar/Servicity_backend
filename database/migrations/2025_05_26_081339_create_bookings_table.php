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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('tasker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->decimal('agreed_price', 12, 2);
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('status', 50)->default('scheduled')->comment("'scheduled', 'in_progress', 'completed', 'canceled', 'disputed'");
            $table->string('payment_status', 50)->default('pending')->comment("'pending', 'paid', 'refunded'");
            $table->timestamps();

        });
        
        // Add check constraints
        DB::statement("
            ALTER TABLE bookings 
            ADD CONSTRAINT bookings_status_check 
            CHECK (status IN ('scheduled', 'in_progress', 'completed', 'canceled', 'disputed'))
        ");

        DB::statement("
            ALTER TABLE bookings 
            ADD CONSTRAINT bookings_payment_status_check 
            CHECK (payment_status IN ('pending', 'paid', 'refunded'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_status_check");
        DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_payment_status_check");

        Schema::dropIfExists('bookings');
    }
};
