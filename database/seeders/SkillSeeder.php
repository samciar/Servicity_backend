<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\Category;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();

        $skills = [
            // Cleaning skills
            [
                'category_id' => $categories->where('name', 'Limpieza')->first()->id,
                'name' => 'Limpieza general del hogar',
                'description' => 'Limpieza completa de todas las áreas del hogar'
            ],
            [
                'category_id' => $categories->where('name', 'Limpieza')->first()->id,
                'name' => 'Limpieza de ventanas',
                'description' => 'Limpieza interior y exterior de ventanas'
            ],
            [
                'category_id' => $categories->where('name', 'Limpieza')->first()->id,
                'name' => 'Limpieza profunda de cocina',
                'description' => 'Limpieza de hornos, refrigeradores y gabinetes'
            ],

            // Assembly skills
            [
                'category_id' => $categories->where('name', 'Ensamblaje')->first()->id,
                'name' => 'Ensamblaje de muebles de oficina',
                'description' => 'Armado de escritorios, sillas y estantería'
            ],
            [
                'category_id' => $categories->where('name', 'Ensamblaje')->first()->id,
                'name' => 'Ensamblaje de muebles de cocina',
                'description' => 'Armado de gabinetes y mesones'
            ],
            [
                'category_id' => $categories->where('name', 'Ensamblaje')->first()->id,
                'name' => 'Ensamblaje de muebles de entretenimiento',
                'description' => 'Armado de estanterías para TV y sistemas de sonido'
            ],

            // Tech support skills
            [
                'category_id' => $categories->where('name', 'Soporte tecnico de computadores')->first()->id,
                'name' => 'Mantenimiento preventivo de PC',
                'description' => 'Limpieza interna y optimización de computadores'
            ],
            [
                'category_id' => $categories->where('name', 'Soporte tecnico de computadores')->first()->id,
                'name' => 'Reparación de hardware',
                'description' => 'Diagnóstico y solución de problemas de hardware'
            ],
            [
                'category_id' => $categories->where('name', 'Soporte tecnico de computadores')->first()->id,
                'name' => 'Instalación de software',
                'description' => 'Instalación y configuración de sistemas operativos y programas'
            ]
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }

        // Assign skills to taskers
        $taskers = \App\Models\User::where('user_type', \App\Models\User::TYPE_TASKER)->get();
        $allSkills = Skill::all();

        foreach ($taskers as $tasker) {
            $randomSkills = $allSkills->random(rand(2, 5));
            $skillsWithProficiency = [];
            
            foreach ($randomSkills as $skill) {
                $skillsWithProficiency[$skill->id] = [
                    'proficiency_level' => rand(3, 5) // 3-5 star proficiency
                ];
            }

            $tasker->skills()->sync($skillsWithProficiency);
        }
    }
}
