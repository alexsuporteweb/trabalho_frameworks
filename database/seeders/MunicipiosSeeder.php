<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarMunicipios;
use Exception;
use Illuminate\Database\Seeder;

class MunicipiosSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarMunicipios $importarMunicipios)
    {
        $importarMunicipios->executar();
    }
}
