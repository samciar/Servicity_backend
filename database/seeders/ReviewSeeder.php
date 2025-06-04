<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        $completedBookings = Booking::where('status', Booking::STATUS_COMPLETED)
            ->whereNotNull('end_time')
            ->get();

        foreach ($completedBookings as $booking) {
            // Client reviews tasker (50% chance)
            if (rand(0, 1)) {
                Review::create([
                    'booking_id' => $booking->id,
                    'reviewer_id' => $booking->task->client_id,
                    'reviewee_id' => $booking->tasker_id,
                    'rating' => rand(4, 5), // Random high rating
                    'comment' => $this->getClientReviewComment()
                ]);
            }

            // Tasker reviews client (50% chance)
            if (rand(0, 1)) {
                Review::create([
                    'booking_id' => $booking->id,
                    'reviewer_id' => $booking->tasker_id,
                    'reviewee_id' => $booking->task->client_id,
                    'rating' => rand(4, 5), // Random high rating
                    'comment' => $this->getTaskerReviewComment()
                ]);
            }
        }
    }

    private function getClientReviewComment(): string
    {
        $comments = [
            "Excelente trabajo, muy profesional!",
            "Cumplió con todas las expectativas, lo recomiendo.",
            "Buen servicio, puntual y eficiente.",
            "Muy satisfecho con el resultado final.",
            "Volvería a contratarlo sin duda."
        ];
        return $comments[array_rand($comments)];
    }

    private function getTaskerReviewComment(): string
    {
        $comments = [
            "Cliente amable y claro en sus requerimientos.",
            "Buen cliente, pago puntual.",
            "Agradable trabajar con él/ella.",
            "Recomendado, muy buen trato.",
            "Cliente respetuoso y comunicativo."
        ];
        return $comments[array_rand($comments)];
    }
}
