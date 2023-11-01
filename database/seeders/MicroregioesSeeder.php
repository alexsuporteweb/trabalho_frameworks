<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarMicroregioes;
use Exception;
use Illuminate\Database\Seeder;

class MicroregioesSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarMicroregioes $importarMicroregioes)
    {
        $importarMicroregioes->executar();
    }
}
