<?php

namespace App\Actions\Imports;

use App\Models\Regioes;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarRegioes
{
    private $regioes;
    private $apiIbgeLocalidadesUrl;

    public function __construct(Regioes $regioes)
    {
        $this->regioes = $regioes;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
    }

    public function executar()
    {
        DB::beginTransaction();
        try {
            $dados = json_decode(
                Http::get(
                    $this->apiIbgeLocalidadesUrl . '/regioes'
                )->body()
            );
            if ($dados) :
                foreach ($dados as $dado) :
                    $id = $dado->id;
                    $sigla = $dado->sigla;
                    $nome = $dado->nome;
                    $retorno = $this->regioes::updateOrCreate(
                        [
                            'id' => $id
                        ],
                        [
                            'sigla' => $sigla,
                            'nome' => $nome,
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
