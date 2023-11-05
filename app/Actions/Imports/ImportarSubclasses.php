<?php

namespace App\Actions\Imports;

use App\Models\Classes;
use App\Models\Divisoes;
use App\Models\Grupos;
use App\Models\Secoes;
use App\Models\Subclasses;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarSubclasses
{
    private $subClasses;
    private $apiIbgeCnaeUrl;

    public function __construct(Subclasses $subClasses)
    {
        $this->subClasses = $subClasses;
        $this->apiIbgeCnaeUrl = env('API_IBGE_CNAE_URL');
    }

    public function executar()
    {
        DB::beginTransaction();
        try {
            $dados = json_decode(
                Http::timeout(260)->get(
                    $this->apiIbgeCnaeUrl . '/subclasses'
                )->body()
            );

            if ($dados) :
                foreach ($dados as $dado) :
                    $codigo = $dado->id;
                    $descricao = $dado->descricao;
                    $classe_id = Classes::where('codigo', $dado->classe->id)->first()->id;
                    $grupo_id = Grupos::where('codigo', $dado->classe->grupo->id)->first()->id;
                    $divisao_id = Divisoes::where('codigo', $dado->classe->grupo->divisao->id)->first()->id;
                    $secao_id = Secoes::where('codigo', $dado->classe->grupo->divisao->secao->id)->first()->id;

                    $retorno = $this->subClasses::updateOrCreate(
                        [
                            'codigo' => $codigo
                        ],
                        [
                            'descricao' => $descricao,
                            'classe_id' => $classe_id,
                            'grupo_id' => $grupo_id,
                            'divisao_id' => $divisao_id,
                            'secao_id' => $secao_id,
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
