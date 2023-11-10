<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarMicrorregioes;
use Exception;
use Illuminate\Database\Seeder;

class MicrorregioesSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarMicrorregioes $importarMicrorregioes)
    {
        $importarMicrorregioes->executar();
    }
}
