<?php

namespace App\Actions\Imports;

use App\Models\Divisoes;
use App\Models\Secoes;
use Exception;
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
        try {
            $url = $this->apiIbgeCnaeUrl . '/divisoes';
            $data = Http::timeout(300)->retry(3, 1000)->get($url);

            $dados = json_decode(Http::get($url)->body(), true);

            if ($data->status() === 200) :
                foreach ($dados as $dado) :
                    $codigo = $dado['id'];
                    $secao_id = Secoes::where('codigo', $dado['secao']['id'])->first()->id;
                    $descricao = $dado['descricao'];

                    $observacoes = '';
                    for ($i = 0; $i < count($dado['observacoes']); $i++) :
                        $observacoes .= "{$dado['observacoes'][$i]}\r\n";
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
            else :
                return response()->json(['message' => 'Erro na solicitação. Status code:'], $dados()->status());
            endif;
        } catch (\Throwable $th) {
            Log::error('Erro durante consulta de API', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }
}
