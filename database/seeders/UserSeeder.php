<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin user
        User::create([
            'name' => 'Admin Servicity',
            'email' => 'admin@servicity.com',
            'password' => Hash::make('password'),
            'phone_number' => '3001234567',
            'address' => 'Calle 123 #45-67, Bogotá',
            'latitude' => 4.710989,
            'longitude' => -74.072092,
            'user_type' => User::TYPE_ADMIN,
            'profile_picture_url' => 'https://example.com/profiles/admin.jpg',
            'bio' => 'Administrador de la plataforma Servicity',
            'hourly_rate' => null,
            'is_available' => false,
            'id_verified' => true
        ]);

        // Sample clients
        $clients = [
            [
                'name' => 'María Gómez',
                'email' => 'maria@example.com',
                'password' => Hash::make('password'),
                'phone_number' => '3101234567',
                'address' => 'Carrera 45 #26-85, Bogotá',
                'user_type' => User::TYPE_CLIENT,
                'bio' => 'Necesito ayuda con tareas del hogar'
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos@example.com',
                'password' => Hash::make('password'),
                'phone_number' => '3202345678',
                'address' => 'Calle 72 #10-25, Bogotá',
                'user_type' => User::TYPE_CLIENT,
                'bio' => 'Buscando profesionales para varios proyectos'
            ]
        ];

        // Sample taskers
        $taskers = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan@example.com',
                'password' => Hash::make('password'),
                'phone_number' => '3153456789',
                'address' => 'Diagonal 23 #45-12, Bogotá',
                'user_type' => User::TYPE_TASKER,
                'bio' => 'Experto en ensamblaje de muebles y reparaciones',
                'hourly_rate' => 25000,
                'is_available' => true
            ],
            [
                'name' => 'Ana López',
                'email' => 'ana@example.com',
                'password' => Hash::make('password'),
                'phone_number' => '3174567890',
                'address' => 'Avenida 68 #13-45, Bogotá',
                'user_type' => User::TYPE_TASKER,
                'bio' => 'Profesional en limpieza de hogares y oficinas',
                'hourly_rate' => 20000,
                'is_available' => true
            ],
            [
                'name' => 'Pedro Martínez',
                'email' => 'pedro@example.com',
                'password' => Hash::make('password'),
                'phone_number' => '3185678901',
                'address' => 'Calle 100 #11-20, Bogotá',
                'user_type' => User::TYPE_TASKER,
                'bio' => 'Técnico en computadores y dispositivos electrónicos',
                'hourly_rate' => 30000,
                'is_available' => true
            ]
        ];

        foreach ($clients as $client) {
            User::create(array_merge($client, [
                'latitude' => 4.6 + (rand(0, 100) / 1000),
                'longitude' => -74.0 - (rand(0, 100) / 1000),
                'profile_picture_url' => 'https://example.com/profiles/'.strtolower(str_replace(' ', '-', $client['name'])).'.jpg',
                'hourly_rate' => null,
                'is_available' => false,
                'id_verified' => rand(0, 1)
            ]));
        }

        foreach ($taskers as $tasker) {
            User::create(array_merge($tasker, [
                'latitude' => 4.6 + (rand(0, 100) / 1000),
                'longitude' => -74.0 - (rand(0, 100) / 1000),
                'profile_picture_url' => 'https://example.com/profiles/'.strtolower(str_replace(' ', '-', $tasker['name'])).'.jpg',
                'id_verified' => rand(0, 1)
            ]));
        }

        // Generate additional random users
        User::factory()->count(10)->create([
            'user_type' => User::TYPE_CLIENT
        ]);

        User::factory()->count(15)->create([
            'user_type' => User::TYPE_TASKER,
            'is_available' => true,
            'hourly_rate' => rand(15000, 40000)
        ]);
    }
}
