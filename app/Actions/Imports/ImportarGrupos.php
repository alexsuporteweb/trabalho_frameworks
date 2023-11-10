<?php

namespace App\Actions\Imports;

use App\Models\Divisoes;
use App\Models\Grupos;
use App\Models\Secoes;
use Exception;
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
        try {
            $url = $this->apiIbgeCnaeUrl . '/grupos';
            $data = Http::timeout(300)->retry(3, 1000)->get($url);

            $dados = json_decode(Http::get($url)->body(), true);

            if ($data->status() === 200) :
                foreach ($dados as $dado) :
                    $codigo = $dado['id'];
                    $divisao_id = Divisoes::where('codigo', $dado['divisao']['id'])->first()->id;
                    $secao_id = Secoes::where('codigo', $dado['divisao']['secao']['id'])->first()->id;
                    $descricao = $dado['descricao'];
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
            else :
                return response()->json(['message' => 'Erro na solicitação. Status code:'], $dados()->status());
            endif;
        } catch (\Throwable $th) {
            Log::error('Erro durante consulta de API', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }
}
