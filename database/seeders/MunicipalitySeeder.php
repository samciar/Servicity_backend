<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Municipality;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::all();

        $municipalities = [
            'Amazonas' => ['Leticia', 'Puerto Nariño', 'El Encanto', 'La Chorrera', 'La Pedrera', 'Mirití-Paraná', 'Puerto Alegría', 'Puerto Arica', 'Puerto Santander', 'Tarapacá'],
            'Antioquia' => ['Medellín', 'Bello', 'Itagüí', 'Envigado', 'Sabaneta', 'Rionegro', 'Marinilla', 'La Ceja', 'Santuario', 'El Retiro', 'Copacabana', 'Girardota', 'Barbosa', 'Caldas', 'La Estrella'],
            'Arauca' => ['Arauca', 'Arauquita', 'Cravo Norte', 'Fortul', 'Puerto Rondón', 'Saravena', 'Tame'],
            'Atlántico' => ['Barranquilla', 'Soledad', 'Malambo', 'Sabanalarga', 'Puerto Colombia', 'Galapa', 'Baranoa', 'Palmar de Varela', 'Polonuevo', 'Santo Tomás'],
            'Bolívar' => ['Cartagena', 'Magangué', 'Turbaco', 'Arjona', 'Carmen de Bolívar', 'San Jacinto', 'San Juan Nepomuceno', 'María la Baja', 'Santa Rosa', 'Simití'],
            'Boyacá' => ['Tunja', 'Duitama', 'Sogamoso', 'Chiquinquirá', 'Paipa', 'Nobsa', 'Samacá', 'Sutamarchán', 'Villa de Leyva', 'Ráquira'],
            'Caldas' => ['Manizales', 'La Dorada', 'Chinchiná', 'Villamaría', 'Neira', 'Palestina', 'Aguadas', 'Anserma', 'Riosucio', 'Salamina'],
            'Caquetá' => ['Florencia', 'Belén de los Andaquíes', 'Cartagena del Chairá', 'Curillo', 'El Doncello', 'El Paujil', 'La Montañita', 'Milán', 'Morelia', 'Puerto Rico'],
            'Casanare' => ['Yopal', 'Aguazul', 'Chámeza', 'Hato Corozal', 'La Salina', 'Maní', 'Monterrey', 'Nunchía', 'Orocué', 'Paz de Ariporo'],
            'Cauca' => ['Popayán', 'Santander de Quilichao', 'El Tambo', 'Patía', 'Puerto Tejada', 'Caldono', 'Inzá', 'Piendamó', 'Silvia', 'Totoró'],
            // Additional departments with representative municipalities...
            'Cesar' => ['Valledupar', 'Aguachica', 'Bosconia', 'Chimichagua', 'Codazzi', 'Curumaní', 'El Copey', 'El Paso', 'Gamarra', 'La Jagua de Ibirico'],
            'Chocó' => ['Quibdó', 'Acandí', 'Alto Baudó', 'Atrato', 'Bagadó', 'Bahía Solano', 'Bajo Baudó', 'Bojayá', 'Cértegui', 'Condoto'],
            'Córdoba' => ['Montería', 'Cereté', 'Sahagún', 'Lorica', 'Planeta Rica', 'Ayapel', 'Buenavista', 'Canalete', 'Chinú', 'Ciénaga de Oro'],
            'Cundinamarca' => ['Bogotá', 'Soacha', 'Chía', 'Zipaquirá', 'Facatativá', 'Girardot', 'Fusagasugá', 'Mosquera', 'Madrid', 'Funza'],
            'Guainía' => ['Inírida', 'Barranco Minas', 'Mapiripana', 'San Felipe', 'Puerto Colombia', 'La Guadalupe', 'Cacahual', 'Pana Pana', 'Morichal'],
            'Guaviare' => ['San José del Guaviare', 'Calamar', 'El Retorno', 'Miraflores'],
            'Huila' => ['Neiva', 'Pitalito', 'Garzón', 'La Plata', 'Campoalegre', 'Palermo', 'San Agustín', 'Aipe', 'Baraya', 'Tesalia'],
            'La Guajira' => ['Riohacha', 'Albania', 'Barrancas', 'Dibulla', 'Distracción', 'El Molino', 'Fonseca', 'Hatonuevo', 'Maicao', 'Manaure'],
            'Magdalena' => ['Santa Marta', 'Ciénaga', 'Fundación', 'Aracataca', 'El Banco', 'Algarrobo', 'Aracataca', 'Ariguaní', 'Cerro San Antonio', 'Chibolo'],
            'Meta' => ['Villavicencio', 'Acacías', 'Granada', 'Puerto López', 'Puerto Gaitán', 'San Martín', 'Puerto Lleras', 'Puerto Rico', 'San Carlos de Guaroa', 'Castilla La Nueva'],
            'Nariño' => ['Pasto', 'Ipiales', 'Tumaco', 'Samaniego', 'La Unión', 'Barbacoas', 'Consacá', 'Contadero', 'Córdoba', 'Cuaspud'],
            'Norte de Santander' => ['Cúcuta', 'Ocaña', 'Pamplona', 'Abrego', 'Bochalema', 'Bucarasica', 'Chinácota', 'Chitagá', 'Convención', 'Cáchira'],
            'Putumayo' => ['Mocoa', 'Colón', 'Orito', 'Puerto Asís', 'Puerto Caicedo', 'Puerto Guzmán', 'San Miguel', 'Santiago', 'Sibundoy', 'Valle del Guamuez'],
            'Quindío' => ['Armenia', 'Calarcá', 'Circasia', 'Filandia', 'Génova', 'La Tebaida', 'Montenegro', 'Pijao', 'Quimbaya', 'Salento'],
            'Risaralda' => ['Pereira', 'Dosquebradas', 'Santa Rosa de Cabal', 'Marsella', 'La Virginia', 'Belén de Umbría', 'Guática', 'Pueblo Rico', 'Quinchía', 'Santuario'],
            'San Andrés y Providencia' => ['San Andrés', 'Providencia'],
            'Santander' => ['Bucaramanga', 'Floridablanca', 'Girón', 'Piedecuesta', 'Barrancabermeja', 'San Gil', 'Socorro', 'Málaga', 'Barbosa', 'Cimitarra'],
            'Sucre' => ['Sincelejo', 'Corozal', 'Tolú', 'Sampués', 'San Marcos', 'San Onofre', 'Coveñas', 'Ovejas', 'Morroa', 'Chalán'],
            'Tolima' => ['Ibagué', 'Espinal', 'Honda', 'Mariquita', 'Melgar', 'Líbano', 'Fresno', 'Guamo', 'Chaparral', 'Planadas'],
            'Valle del Cauca' => ['Cali', 'Palmira', 'Buenaventura', 'Tuluá', 'Buga', 'Cartago', 'Jamundí', 'Yumbo', 'Florida', 'Pradera'],
            'Vaupés' => ['Mitú', 'Carurú', 'Pacoa', 'Papunaua', 'Taraira', 'Yavaraté'],
            'Vichada' => ['Puerto Carreño', 'La Primavera', 'Santa Rosalía', 'Cumaribo']
        ];

        foreach ($departments as $department) {
            if (isset($municipalities[$department->name])) {
                foreach ($municipalities[$department->name] as $municipality) {
                    Municipality::create([
                        'name' => $municipality,
                        'department_id' => $department->id
                    ]);
                }
            }
        }
    }
}
