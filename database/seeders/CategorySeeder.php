<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => "Jardinería",
                'description' => "Servicios de jardinería, poda de árboles, mantenimiento de jardines y áreas verdes",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Reparaciones del Hogar",
                'description' => "Reparaciones generales del hogar, arreglos menores y mantenimiento preventivo",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Armado de Muebles",
                'description' => "Ensamblaje y armado de muebles, estanterías, armarios y mobiliario en general",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Limpieza del Hogar",
                'description' => "Servicios de limpieza residencial, oficinas y espacios comerciales",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Cuidado de Niños",
                'description' => "Servicios de cuidado y atención de niños, niñeras y babysitter",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Cuidado de Ancianos",
                'description' => "Atención y cuidado de adultos mayores, acompañamiento y asistencia",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Mudanzas",
                'description' => "Servicios de mudanza, transporte de muebles y empaque de pertenencias",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Pintura",
                'description' => "Servicios de pintura residencial y comercial, preparación de superficies",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Plomería",
                'description' => "Reparaciones de plomería, instalaciones sanitarias y mantenimiento de tuberías",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Electricidad",
                'description' => "Servicios eléctricos, instalaciones, reparaciones y mantenimiento eléctrico",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Carpintería",
                'description' => "Trabajos de carpintería, fabricación y reparación de muebles de madera",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Organización del Hogar",
                'description' => "Servicios de organización de espacios, closet, almacenamiento y orden",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Cocina y Alimentación",
                'description' => "Servicios de cocina, preparación de alimentos y catering básico",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Mascotas",
                'description' => "Cuidado de mascotas, paseo de perros y atención animal doméstica",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Transporte",
                'description' => "Servicios de transporte local, mensajería y entregas rápidas",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => "Soporte Técnico de Computadores",
                'description' => "Servicios de mantenimiento y reparación de computadores e impresoras",
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert($category);
        }
    }
}
