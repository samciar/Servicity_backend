<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Amazonas', 'Antioquia', 'Arauca', 'Atlántico', 'Bolívar',
            'Boyacá', 'Caldas', 'Caquetá', 'Casanare', 'Cauca',
            'Cesar', 'Chocó', 'Córdoba', 'Cundinamarca', 'Guainía',
            'Guaviare', 'Huila', 'La Guajira', 'Magdalena', 'Meta',
            'Nariño', 'Norte de Santander', 'Putumayo', 'Quindío',
            'Risaralda', 'San Andrés y Providencia', 'Santander',
            'Sucre', 'Tolima', 'Valle del Cauca', 'Vaupés', 'Vichada'
        ];

        foreach ($departments as $department) {
            Department::create(['name' => $department]);
        }
    }
}
