<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarPaises;
use Exception;
use Illuminate\Database\Seeder;

class PaisesSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarPaises $importarPaises)
    {
        $importarPaises->executar();
    }
}
