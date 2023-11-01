<?php

namespace App\Actions\Imports;

use App\Models\Divisoes;
use App\Models\Grupos;
use App\Models\Secoes;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarGrupos
{
    private $grupos;
    private $apiIbgeCnaeUrl;

    public function __construct(Grupos $grupos)
    {
        $this->grupos = $grupos;
        $this->apiIbgeCnaeUrl = env('API_IBGE_CNAE_URL');
    }

    public function executar()
    {
        DB::beginTransaction();
        try {
            $dados = json_decode(
                Http::get(
                    $this->apiIbgeCnaeUrl . '/grupos'
                )->body()
            );
            if ($dados) :
                foreach ($dados as $dado) :
                    $codigo = $dado->id;
                    $divisao_id = Divisoes::where('codigo', $dado->divisao->id)->first()->id;
                    $secao_id = Secoes::where('codigo', $dado->divisao->secao->id)->first()->id;
                    $descricao = $dado->descricao;
                    $retorno = $this->grupos::updateOrCreate(
                        [
                            'codigo' => $codigo
                        ],
                        [
                            'divisao_id' => $divisao_id,
                            'secao_id' => $secao_id,
                            'descricao' => $descricao,
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
