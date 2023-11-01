<?php

namespace Database\Seeders;

use App\Actions\Imports\ImportarSecoes;
use Exception;
use Illuminate\Database\Seeder;

class SecoesSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarSecoes $importarSecoes)
    {
        $importarSecoes->executar();
    }
}
