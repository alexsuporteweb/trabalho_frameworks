<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserDefaultSeeder::class,
            PaisesSeeder::class,
            RegioesSeeder::class,
            EstadosSeeder::class,
            RegioesIntermediariasSeeder::class,
            MesorregioesSeeder::class,
            MicrorregioesSeeder::class,
            RegioesImediatasSeeder::class,
            MunicipiosSeeder::class,
            SecoesSeeder::class,
            DivisoesSeeder::class,
            GruposSeeder::class,
            ClassesSeeder::class,
            SubclassesSeeder::class,
        ]);
    }
}
