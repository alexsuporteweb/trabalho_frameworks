<?php

namespace App\Actions\Imports;

use App\Models\Divisoes;
use App\Models\Grupos;
use App\Models\Secoes;
use App\Models\Classes;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarClasses
{
    private $classes;
    private $apiIbgeCnaeUrl;

    public function __construct(Classes $classes)
    {
        $this->classes = $classes;
        $this->apiIbgeCnaeUrl = env('API_IBGE_CNAE_URL');
    }

    public function executar()
    {
        DB::beginTransaction();
        try {
            $dados = json_decode(
                Http::get(
                    $this->apiIbgeCnaeUrl . '/classes'
                )->body()
            );
            if ($dados) :
                foreach ($dados as $dado) :
                    $codigo = $dado->id;
                    $grupo_id = Grupos::where('codigo', $dado->grupo->id)->first()->id;
                    $divisao_id = Divisoes::where('codigo', $dado->grupo->divisao->id)->first()->id;
                    $secao_id = Secoes::where('codigo', $dado->grupo->divisao->secao->id)->first()->id;
                    $descricao = $dado->descricao;

                    $observacoes = '';
                    for ($i = 0; $i < count($dado->observacoes); $i++) :
                        $observacoes .= "{$dado->observacoes[$i]}\r\n";
                    endfor;

                    $retorno = $this->classes::updateOrCreate(
                        [
                            'codigo' => $codigo
                        ],
                        [
                            'grupo_id' => $grupo_id,
                            'divisao_id' => $divisao_id,
                            'secao_id' => $secao_id,
                            'descricao' => $descricao,
                            'observacoes' => $observacoes,
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