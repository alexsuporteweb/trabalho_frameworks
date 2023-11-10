<?php

namespace App\Actions\Imports;

use App\Models\Secoes;
use Exception;
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
        try {
            $url = $this->apiIbgeCnaeUrl . '/secoes';
            $data = Http::timeout(300)->retry(3, 1000)->get($url);

            $dados = json_decode(Http::get($url)->body(), true);

            if ($data->status() === 200) :
                foreach ($dados as $dado) :
                    $codigo = $dado['id'];
                    $descricao = $dado['descricao'];

                    $observacoes = '';
                    for ($i = 0; $i < count($dado['observacoes']); $i++) :
                        $observacoes .= "{$dado['observacoes'][$i]}\r\n";
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
            else :
                return response()->json(['message' => 'Erro na solicitação. Status code:'], $dados()->status());
            endif;
        } catch (\Throwable $th) {
            Log::error('Erro durante consulta de API', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }
}
