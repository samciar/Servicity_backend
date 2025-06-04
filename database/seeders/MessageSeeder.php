<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run()
    {
        $tasks = Task::has('bids')->get();

        foreach ($tasks as $task) {
            $client = $task->client;
            $taskers = $task->bids->pluck('tasker')->unique();

            foreach ($taskers as $tasker) {
                // Initial message from tasker
                Message::create([
                    'sender_id' => $tasker->id,
                    'receiver_id' => $client->id,
                    'message' => $this->getInitialMessage($task),
                    'booking_id' => $task->booking?->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 3))
                ]);

                // Response from client (75% chance)
                if (rand(0, 3) > 0) {
                    Message::create([
                    'sender_id' => $client->id,
                    'receiver_id' => $tasker->id,
                    'message' => $this->getClientResponse(),
                    'booking_id' => $task->booking?->id,
                        'created_at' => Carbon::now()->subDays(rand(0, 2))
                    ]);

                    // Follow-up from tasker (50% chance)
                    if (rand(0, 1)) {
                        Message::create([
                    'sender_id' => $tasker->id,
                    'receiver_id' => $client->id,
                    'message' => $this->getFollowUpMessage(),
                    'booking_id' => $task->booking?->id,
                            'created_at' => Carbon::now()->subDays(rand(0, 1))
                        ]);
                    }
                }
            }
        }
    }

    private function getInitialMessage(Task $task): string
    {
        $messages = [
            "Hola, estoy interesado en tu tarea de {$task->title}. ¿Podrías darme más detalles?",
            "Buen día, tengo experiencia en este tipo de trabajo. ¿Qué más necesitas que sepa?",
            "Hola, me gustaría ayudarte con {$task->title}. ¿Está todavía disponible?",
            "Hola, vi tu publicación sobre {$task->title} y me interesa. ¿Podemos hablar más sobre el trabajo?",
            "Buenas, estoy disponible para ayudarte con {$task->title}. ¿Cuándo necesitas que se realice?"
        ];
        return $messages[array_rand($messages)];
    }

    private function getClientResponse(): string
    {
        $responses = [
            "Gracias por tu interés. ¿Cuál es tu disponibilidad?",
            "Sí, todavía necesito ayuda con esto. ¿Tienes experiencia previa?",
            "Perfecto, ¿podrías explicarme cómo abordarías este trabajo?",
            "Gracias por contactarme. ¿Cuál sería tu tarifa exacta?",
            "Me alegra que estés interesado. ¿Podrías enviarme algunas referencias?"
        ];
        return $responses[array_rand($responses)];
    }

    private function getFollowUpMessage(): string
    {
        $messages = [
            "Claro, tengo disponibilidad inmediata. ¿Prefieres que nos reunamos para discutir los detalles?",
            "Sí, tengo bastante experiencia. Te puedo enviar ejemplos de trabajos similares.",
            "Por lo general cobro según lo acordado en mi oferta. ¿Te parece bien?",
            "Puedo empezar mañana si estás de acuerdo con los términos.",
            "Trabajo de manera profesional y garantizo calidad. ¿Qué más necesitas saber?"
        ];
        return $messages[array_rand($messages)];
    }
}
