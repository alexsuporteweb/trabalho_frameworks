<?php

namespace Database\Seeders;

use App\Actions\Imports\importardi;
use App\Actions\Imports\ImportarDivisoes;
use Exception;
use Illuminate\Database\Seeder;

class DivisoesSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarDivisoes $importarDivisoes)
    {
        $importarDivisoes->executar();
    }
}
