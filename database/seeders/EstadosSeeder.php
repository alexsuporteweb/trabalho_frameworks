<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarEstados;
use Exception;
use Illuminate\Database\Seeder;

class EstadosSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarEstados $importarEstados)
    {
        $importarEstados->executar();
    }
}
