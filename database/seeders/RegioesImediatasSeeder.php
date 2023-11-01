<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarRegioesImediatas;
use Exception;
use Illuminate\Database\Seeder;

class RegioesImediatasSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarRegioesImediatas $importarRegioesImediatas)
    {
        $importarRegioesImediatas->executar();
    }
}
