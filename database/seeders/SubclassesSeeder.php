<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarSubclasses;
use Exception;
use Illuminate\Database\Seeder;

class SubclassesSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarSubclasses $importarSubclasses)
    {
        $importarSubclasses->executar();
    }
}
