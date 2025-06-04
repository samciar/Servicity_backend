<?php

namespace Database\Seeders;

use App\Models\Bid;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class BidSeeder extends Seeder
{
    public function run()
    {
        $tasks = Task::where('status', Task::STATUS_OPEN)->get();
        $taskers = User::where('user_type', User::TYPE_TASKER)
            ->where('is_available', true)
            ->get();

        foreach ($tasks as $task) {
            // Get taskers with matching skills
            $matchingTaskers = $taskers->filter(function($tasker) use ($task) {
                return $tasker->skills->pluck('id')->intersect($task->skills->pluck('id'))->isNotEmpty();
            });

            if ($matchingTaskers->isEmpty()) {
                continue;
            }

            // Create 2-4 bids per task from qualified taskers
            $bidsCount = rand(2, min(4, $matchingTaskers->count()));
            $selectedTaskers = $matchingTaskers->random($bidsCount);

            foreach ($selectedTaskers as $tasker) {
                // Calculate bid amount based on task budget
                $baseAmount = $task->budget_amount;
                if ($task->budget_type === Task::BUDGET_FIXED) {
                    $randomFactor = 0.8 + (rand(0, 40) / 100);
                    $bidAmount = $baseAmount * $randomFactor;
                } else {
                    $randomFactor = 0.9 + (rand(0, 20) / 100);
                    $bidAmount = $baseAmount * $randomFactor;
                }

                Bid::create([
                    'task_id' => $task->id,
                    'tasker_id' => $tasker->id,
                    'bid_amount' => round($bidAmount, 2),
                    'message' => $this->getBidMessage($task, $tasker),
                    'status' => Bid::STATUS_PENDING
                ]);
            }

            // Randomly accept one bid for some tasks
            if (rand(0, 1) && $task->bids->isNotEmpty()) {
                $bidToAccept = $task->bids->random();
                $bidToAccept->update(['status' => Bid::STATUS_ACCEPTED]);
                $task->update(['status' => Task::STATUS_ASSIGNED]);
            }
        }
    }

    private function getBidMessage(Task $task, User $tasker): string
    {
        $messages = [
            "Hola, tengo experiencia en este tipo de trabajo y puedo ayudarte. Mi tarifa es competitiva.",
            "He realizado trabajos similares antes y estaría encantado de ayudarte con esta tarea.",
            "Puedo completar este trabajo según tus requerimientos. Tengo buenas referencias.",
            "Soy especialista en este tipo de trabajos y puedo ofrecerte un servicio de calidad.",
            "Estoy disponible para realizar este trabajo en la fecha solicitada."
        ];

        return $messages[array_rand($messages)];
    }
}
