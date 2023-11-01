<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarRegioesIntermediarias;
use Exception;
use Illuminate\Database\Seeder;

class RegioesIntermediariasSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarRegioesIntermediarias $importarRegioesIntermediarias)
    {
        $importarRegioesIntermediarias->executar();
    }
}
