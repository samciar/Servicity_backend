<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Booking;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run()
    {
        $completedBookings = Booking::where('status', 'completed')
            ->where('payment_status', 'pending')
            ->get();

        foreach ($completedBookings as $booking) {
            $paymentStatus = $this->getRandomPaymentStatus();
            $paymentDate = $paymentStatus === 'completed'
                ? Carbon::now()->subHours(rand(1, 24))
                : null;

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->bid->bid_amount,
                'status' => $paymentStatus,
                'method' => $this->getRandomPaymentMethod(),
                'transaction_id' => 'PAY-' . strtoupper(uniqid()),
                'completed_at' => $paymentDate,
                'client_id' => $booking->task->client_id,
                'tasker_id' => $booking->tasker_id
            ]);

            // Create notification for payment
            if ($paymentStatus === 'completed') {
                Notification::create([
                    'user_id' => $booking->tasker_id,
                    'user_id' => $booking->tasker_id,
                    'type' => 'payment_completed',
                    'data' => json_encode([
                        'message' => "Se ha completado el pago por tu trabajo en '{$booking->task->title}'",
                        'booking_id' => $booking->id,
                        'amount' => $booking->bid->bid_amount
                    ]),
                    'read_at' => null,
                    'created_at' => $paymentDate,
                    'updated_at' => $paymentDate
                ]);
            }
        }
    }

    private function getRandomPaymentStatus(): string
    {
        $statuses = [
            'pending',
            'completed',
            'failed',
            'refunded'
        ];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomPaymentMethod(): string
    {
        $methods = [
            'credit_card',
            'debit_card',
            'bank_transfer',
            'cash',
            'paypal'
        ];
        return $methods[array_rand($methods)];
    }
}
