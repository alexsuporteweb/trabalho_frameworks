<?php

namespace App\Actions\Imports;

use App\Models\RegioesIntermediarias;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarRegioesIntermediarias
{
    private $regioesIntermediarias;
    private $apiIbgeLocalidadesUrl;

    public function __construct(RegioesIntermediarias $regioesIntermediarias)
    {
        $this->regioesIntermediarias = $regioesIntermediarias;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
    }

    public function executar()
    {
        DB::beginTransaction();
        try {
            $dados = json_decode(
                Http::get(
                    $this->apiIbgeLocalidadesUrl . '/regioes-intermediarias'
                )->body()
            );
            if ($dados) :
                foreach ($dados as $dado) :
                    $id = $dado->id;
                    $nome = $dado->nome;
                    $estado_id = $dado->UF->id;
                    $regiao_id = $dado->UF->regiao->id;
                    $retorno = $this->regioesIntermediarias::updateOrCreate(
                        [
                            'id' => $id
                        ],
                        [
                            'nome' => $nome,
                            'estado_id' => $estado_id,
                            'regiao_id' => $regiao_id,
                        ]
                    );
                endforeach;
            endif;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Erro durante consulta de API', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }
}
