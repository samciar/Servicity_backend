<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run()
    {
        $clients = User::where('user_type', User::TYPE_CLIENT)->get();
        $categories = Category::all();
        $skills = Skill::all();

        $tasks = [
            // Cleaning tasks
            [
                'client_id' => $clients->random()->id,
                'category_id' => $categories->where('name', 'Limpieza')->first()->id,
                'title' => 'Limpieza semanal de apartamento',
                'description' => 'Necesito ayuda con la limpieza semanal de mi apartamento de 2 habitaciones',
                'budget_type' => Task::BUDGET_FIXED,
                'budget_amount' => 80000,
                'location' => 'Carrera 45 #26-85, Bogotá',
                'latitude' => 4.648283,
                'longitude' => -74.096422,
                'preferred_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'preferred_time' => '09:00',
                'status' => Task::STATUS_OPEN
            ],
            [
                'client_id' => $clients->random()->id,
                'category_id' => $categories->where('name', 'Limpieza')->first()->id,
                'title' => 'Limpieza profunda de cocina',
                'description' => 'Limpieza completa de cocina incluyendo horno y refrigerador',
                'budget_type' => Task::BUDGET_HOURLY,
                'budget_amount' => 20000,
                'location' => 'Calle 72 #10-25, Bogotá',
                'latitude' => 4.656421,
                'longitude' => -74.059832,
                'preferred_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'preferred_time' => '14:00',
                'status' => Task::STATUS_OPEN
            ],

            // Assembly tasks
            [
                'client_id' => $clients->random()->id,
                'category_id' => $categories->where('name', 'Ensamblaje')->first()->id,
                'title' => 'Armar escritorio de oficina',
                'description' => 'Necesito ayuda para armar un escritorio de oficina que compré recientemente',
                'budget_type' => Task::BUDGET_FIXED,
                'budget_amount' => 50000,
                'location' => 'Calle 100 #11-20, Bogotá',
                'latitude' => 4.683511,
                'longitude' => -74.047153,
                'preferred_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'preferred_time' => '10:00',
                'status' => Task::STATUS_OPEN
            ],
            [
                'client_id' => $clients->random()->id,
                'category_id' => $categories->where('name', 'Ensamblaje')->first()->id,
                'title' => 'Armar estantería para sala',
                'description' => 'Ensamblar estantería modular para sala de estar',
                'budget_type' => Task::BUDGET_HOURLY,
                'budget_amount' => 25000,
                'location' => 'Avenida 68 #13-45, Bogotá',
                'latitude' => 4.669832,
                'longitude' => -74.078421,
                'preferred_date' => Carbon::now()->addDays(4)->format('Y-m-d'),
                'preferred_time' => '15:00',
                'status' => Task::STATUS_OPEN
            ],

            // Tech support tasks
            [
                'client_id' => $clients->random()->id,
                'category_id' => $categories->where('name', 'Soporte tecnico de computadores')->first()->id,
                'title' => 'Mantenimiento de computador portátil',
                'description' => 'Mi portátil está lento y necesita mantenimiento',
                'budget_type' => Task::BUDGET_FIXED,
                'budget_amount' => 60000,
                'location' => 'Diagonal 23 #45-12, Bogotá',
                'latitude' => 4.612345,
                'longitude' => -74.065432,
                'preferred_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'preferred_time' => '11:00',
                'status' => Task::STATUS_OPEN
            ],
            [
                'client_id' => $clients->random()->id,
                'category_id' => $categories->where('name', 'Soporte tecnico de computadores')->first()->id,
                'title' => 'Instalación de Windows y programas',
                'description' => 'Necesito instalar Windows 10 y paquete Office en computador nuevo',
                'budget_type' => Task::BUDGET_HOURLY,
                'budget_amount' => 30000,
                'location' => 'Carrera 7 #40-25, Bogotá',
                'latitude' => 4.635678,
                'longitude' => -74.078765,
                'preferred_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'preferred_time' => '13:00',
                'status' => Task::STATUS_OPEN
            ]
        ];

        foreach ($tasks as $task) {
            $newTask = Task::create($task);
            
            // Assign relevant skills to tasks
            $categorySkills = $skills->where('category_id', $task['category_id']);
            if ($categorySkills->isNotEmpty()) {
                $newTask->skills()->attach(
                    $categorySkills->random(rand(1, 3))->pluck('id')
                );
            }
        }

        // Generate additional random tasks
        Task::factory()->count(20)->create([
            'status' => Task::STATUS_OPEN
        ]);
    }
}
