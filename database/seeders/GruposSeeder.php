<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarGrupos;
use Exception;
use Illuminate\Database\Seeder;

class GruposSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarGrupos $importarGrupos)
    {
        $importarGrupos->executar();
    }
}
