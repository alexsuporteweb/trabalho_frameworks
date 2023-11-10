<?php

namespace App\Actions\Imports;

use App\Models\Mesorregioes;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarMesorregioes
{
    private $mesorregioes;
    private $apiIbgeLocalidadesUrl;

    public function __construct(Mesorregioes $mesorregioes)
    {
        $this->mesorregioes = $mesorregioes;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
    }

    public function executar()
    {
        try {
            $url = $this->apiIbgeLocalidadesUrl . '/mesorregioes';
            $data = Http::timeout(300)->retry(3, 1000)->get($url);

            $dados = json_decode(Http::get($url)->body(), true);

            if ($data->status() === 200) :
                foreach ($dados as $dado) :
                    $id = $dado['id'];
                    $nome = $dado['nome'];
                    $estado_id = $dado['UF']['id'];
                    $regiao_id = $dado['UF']['regiao']['id'];
                    $retorno = $this->mesorregioes::updateOrCreate(
                        [
                            'id' => $id
                        ],
                        [
                            'nome' => $nome,
                            'estado_id' => $estado_id,
                            'regiao_id' => $regiao_id,
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
