<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();



        Rol::create([
            'nombre' => 'Admin',
        ]);
        Rol::create([
            'nombre' => 'Usuario',
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('123123'),
            'rol_id' => 1,
        ]);


        $categorias = [
            [
                'nombre' => 'Restaurantes',
                'descripcion' => 'Encuentra los mejores restaurantes cerca de ti.',
                'icono' => 'fa-solid fa-utensils',
            ],
            [
                'nombre' => 'Cafeterías',
                'descripcion' => 'Disfruta de un buen café en las mejores cafeterías.',
                'icono' => 'fa-solid fa-mug-saucer',
            ],
            [
                'nombre' => 'Tiendas',
                'descripcion' => 'Compra en las mejores tiendas de tu ciudad.',
                'icono' => 'fa-solid fa-store',
            ],
            [
                'nombre' => 'Gimnasios',
                'descripcion' => 'Mantente en forma en los mejores gimnasios.',
                'icono' => 'fa-solid fa-dumbbell',
            ],
            [
                'nombre' => 'Salones de belleza',
                'descripcion' => 'Cuida tu imagen en los mejores salones de belleza.',
                'icono' => 'fa-solid fa-scissors',
            ]
        ];

        Categoria::insert($categorias);
    }
}
