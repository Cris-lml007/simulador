<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '12345678'
        ]);

        Person::create([
            'ci' => 7322343,
            'surname' => 'Martinez Apaza',
            'name' => 'Juan',
            'cellular' => 61831311,
            'range' => 'Sanrgento'
        ]);
        Person::create([
            'ci' => 7322334,
            'surname' => 'Garcia Mendoza',
            'name' => 'Pedro',
            'cellular' => 61857155,
            'range' => 'Sanrgento'
        ]);
        Person::create([
            'ci' => 7322343,
            'surname' => 'Mamani Mamani',
            'name' => 'Marta',
            'cellular' => 61832344,
            'range' => 'Sargento'
        ]);

        // Person::factory(100)->create();

        $people = collect();

        for ($i = 0; $i < 100; $i++) {
            do {
                $person = [
                    'ci' => $i+10000,
                    'surname' => fake()->lastName(),
                    'name' => fake()->name(),
                    'cellular' => $i+7000000,
                    'range' => ['sargento', 'teniente', 'capitan', 'mayor'][rand(0, 3)],
                ];
            } while ($people->contains('ci', $person['ci']) || $people->contains('cellular', $person['cellular']));

            $people->push($person);
        }

        // Inserta en la base de datos en un solo lote
        Person::upsert($people,[],[]);
        // DB::table('people')->insert($people->toArray());
    }
}
