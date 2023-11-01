<?php

namespace App\Actions\Imports;

use App\Models\Divisoes;
use App\Models\Secoes;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarDivisoes
{
    private $divisoes;
    private $apiIbgeCnaeUrl;

    public function __construct(Divisoes $divisoes)
    {
        $this->divisoes = $divisoes;
        $this->apiIbgeCnaeUrl = env('API_IBGE_CNAE_URL');
    }

    public function executar()
    {
        DB::beginTransaction();
        try {
            $dados = json_decode(
                Http::get(
                    $this->apiIbgeCnaeUrl . '/divisoes'
                )->body()
            );
            if ($dados) :
                foreach ($dados as $dado) :
                    $codigo = $dado->id;
                    $secao_id = Secoes::where('codigo', $dado->secao->id)->first()->id;
                    $descricao = $dado->descricao;

                    $observacoes = '';
                    for ($i = 0; $i < count($dado->observacoes); $i++) :
                        $observacoes .= "{$dado->observacoes[$i]}\r\n";
                    endfor;

                    $retorno = $this->divisoes::updateOrCreate(
                        [
                            'codigo' => $codigo
                        ],
                        [
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
