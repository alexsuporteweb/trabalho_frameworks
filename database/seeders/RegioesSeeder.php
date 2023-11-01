<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarRegioes;
use Exception;
use Illuminate\Database\Seeder;

class RegioesSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarRegioes $importarRegioes)
    {
        $importarRegioes->executar();
    }
}
