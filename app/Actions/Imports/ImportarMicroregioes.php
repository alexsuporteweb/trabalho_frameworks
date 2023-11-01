<?php

namespace App\Actions\Imports;

use App\Models\Microregioes;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarMicroregioes
{
    private $microregioes;
    private $apiIbgeLocalidadesUrl;

    public function __construct(Microregioes $microregioes)
    {
        $this->microregioes = $microregioes;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
    }

    public function executar()
    {
        DB::beginTransaction();
        try {
            $dados = json_decode(
                Http::get(
                    $this->apiIbgeLocalidadesUrl . '/microrregioes'
                )->body()
            );
            if ($dados) :
                foreach ($dados as $dado) :
                    $id = $dado->id;
                    $nome = $dado->nome;
                    $mesorregiao_id = $dado->mesorregiao->id;
                    $estado_id = $dado->mesorregiao->UF->id;
                    $regiao_id = $dado->mesorregiao->UF->regiao->id;
                    $retorno = $this->microregioes::updateOrCreate(
                        [
                            'id' => $id
                        ],
                        [
                            'nome' => $nome,
                            'mesorregiao_id' => $mesorregiao_id,
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
