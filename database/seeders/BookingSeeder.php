<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Bid;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BookingSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $acceptedBids = Bid::where('status', Bid::STATUS_ACCEPTED)->get();

        foreach ($acceptedBids as $bid) {
            $task = $bid->task;
            $durationHours = $this->getDurationForTask($task);
            

            $startTime = Carbon::parse($task->preferred_date)->setTime(
                Carbon::parse($task->preferred_time)->hour,
                Carbon::parse($task->preferred_time)->minute,
                Carbon::parse($task->preferred_time)->second
            );
            
            Booking::create([
                'task_id' => $task->id,
                'tasker_id' => $bid->tasker_id,
                'client_id' => $task->client_id,
                'agreed_price' => $bid->bid_amount,
                'start_time' => $startTime->format('Y-m-d H:i:s'),
                'end_time' => $startTime->copy()->addHours($durationHours)->format('Y-m-d H:i:s'),
                'status' => $this->getRandomStatus(),
                'payment_status' => $faker->randomElement([
                    Booking::PAYMENT_PENDING,
                    Booking::PAYMENT_PAID,
                    Booking::PAYMENT_REFUNDED
                ])
            ]);
        }
    }

    private function getDurationForTask($task): int
    {
        if ($task->budget_type === 'fixed') {
            return rand(1, 4); // 1-4 hours for fixed price tasks
        }
        return ceil($task->budget_amount / 20000); // Estimate based on hourly rate
    }

    private function getRandomStatus(): string
    {
        $statuses = [
            Booking::STATUS_SCHEDULED,
            Booking::STATUS_IN_PROGRESS,
            Booking::STATUS_COMPLETED,
            Booking::STATUS_CANCELED
        ];
        return $statuses[array_rand($statuses)];
    }
}
