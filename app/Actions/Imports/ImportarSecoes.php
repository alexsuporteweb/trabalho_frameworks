<?php

namespace App\Actions\Imports;

use App\Models\Secoes;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarSecoes
{
    private $secoes;
    private $apiIbgeCnaeUrl;

    public function __construct(Secoes $secoes)
    {
        $this->secoes = $secoes;
        $this->apiIbgeCnaeUrl = env('API_IBGE_CNAE_URL');
    }

    public function executar()
    {
        DB::beginTransaction();
        try {
            $dados = json_decode(
                Http::get(
                    $this->apiIbgeCnaeUrl . '/secoes'
                )->body()
            );
            if ($dados) :
                foreach ($dados as $dado) :
                    $codigo = $dado->id;
                    $descricao = $dado->descricao;

                    $observacoes = '';
                    for ($i = 0; $i < count($dado->observacoes); $i++) :
                        $observacoes .= "{$dado->observacoes[$i]}\r\n";
                    endfor;

                    $retorno = $this->secoes::updateOrCreate(
                        [
                            'codigo' => $codigo
                        ],
                        [
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
