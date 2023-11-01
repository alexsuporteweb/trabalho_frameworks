<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarClasses;
use Exception;
use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarClasses $importarClasses)
    {
        $importarClasses->executar();
    }
}
