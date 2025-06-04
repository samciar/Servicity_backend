<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use App\Models\Task;
use App\Models\Bid;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $tasks = Task::has('bids')->get();

        // Notifications for task owners
        foreach ($tasks as $task) {
            // New bid notification
            foreach ($task->bids as $bid) {
                Notification::create([
                    'user_id' => $task->client_id,
                    'type' => 'bid_received',
                    'data' => json_encode([
                        'message' => "Has recibido una nueva oferta de {$bid->tasker->name} para tu tarea '{$task->title}'",
                        'bid_id' => $bid->id
                    ]),
                    'read_at' => rand(0, 1) ? Carbon::now() : null,
                    'created_at' => $bid->created_at,
                    'updated_at' => $bid->created_at
                ]);
            }

            // Bid accepted notification
            if ($task->status === 'assigned') {
                $acceptedBid = $task->bids->where('status', 'accepted')->first();
                Notification::create([
                    'user_id' => $acceptedBid->tasker_id,
                    'type' => 'bid_accepted',
                    'data' => json_encode([
                        'message' => "Tu oferta para '{$task->title}' ha sido aceptada",
                        'bid_id' => $acceptedBid->id
                    ]),
                    'read_at' => null,
                    'created_at' => $acceptedBid->updated_at,
                    'updated_at' => $acceptedBid->updated_at
                ]);
            }
        }

        // Booking status notifications
        foreach ($users as $user) {
            if ($user->taskerBookings && $user->taskerBookings->isNotEmpty()) {
                foreach ($user->taskerBookings as $booking) {
                    $statusMessages = [
                        'scheduled' => "Tu reserva para '{$booking->task->title}' ha sido programada",
                        'in_progress' => "La reserva para '{$booking->task->title}' ha comenzado",
                        'completed' => "¡Has completado la reserva para '{$booking->task->title}'!",
                        'canceled' => "La reserva para '{$booking->task->title}' ha sido cancelada"
                    ];

                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'booking_update',
                        'data' => json_encode([
                            'message' => $statusMessages[$booking->status],
                            'booking_id' => $booking->id
                        ]),
                        'read_at' => rand(0, 1) ? Carbon::now() : null,
                        'created_at' => $booking->updated_at,
                        'updated_at' => $booking->updated_at
                    ]);
                }
            }
        }

        // Review notifications
        foreach ($users as $user) {
            if ($user->receivedReviews && $user->receivedReviews->isNotEmpty()) {
                foreach ($user->receivedReviews as $review) {
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'new_review',
                        'data' => json_encode([
                            'message' => "Has recibido una nueva reseña de {$review->reviewer->name}",
                            'review_id' => $review->id
                        ]),
                        'read_at' => rand(0, 1) ? Carbon::now() : null,
                        'created_at' => $review->created_at,
                        'updated_at' => $review->created_at
                    ]);
                }
            }
        }
    }
}
