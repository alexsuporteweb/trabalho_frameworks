<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarMesorregioes;
use Exception;
use Illuminate\Database\Seeder;

class MesorregioesSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarMesorregioes $importarMesorregioes)
    {
        $importarMesorregioes->executar();
    }
}
