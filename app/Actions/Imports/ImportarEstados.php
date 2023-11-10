<?php

namespace App\Actions\Imports;

use App\Models\Estados;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarEstados
{
    private $estados;
    private $apiIbgeLocalidadesUrl;

    public function __construct(Estados $estados)
    {
        $this->estados = $estados;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
    }

    public function executar()
    {
        try {
            $url = $this->apiIbgeLocalidadesUrl . '/estados';
            $data = Http::timeout(300)->retry(3, 1000)->get($url);

            $dados = json_decode(Http::get($url)->body(), true);

            if ($data->status() === 200) :
                foreach ($dados as $dado) :
                    $id = $dado['id'];
                    $sigla = $dado['sigla'];
                    $nome = $dado['nome'];
                    $regiao_id = $dado['regiao']['id'];
                    $retorno = $this->estados::updateOrCreate(
                        [
                            'id' => $id
                        ],
                        [
                            'sigla' => $sigla,
                            'nome' => $nome,
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
