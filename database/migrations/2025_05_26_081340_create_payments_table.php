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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('payer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('payee_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('transaction_id')->nullable()->unique();
            $table->string('payment_method', 50)->nullable();
            $table->string('status', 50)->default('pending')->comment("'pending', 'completed', 'failed', 'refunded'");
            $table->timestamp('processed_at')->useCurrent();
            $table->timestamps();

        });
        
        // Add check constraint for status
        DB::statement("
            ALTER TABLE payments 
            ADD CONSTRAINT payments_status_check 
            CHECK (status IN ('pending', 'completed', 'failed', 'refunded'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_status_check");

        Schema::dropIfExists('payments');
    }
};
