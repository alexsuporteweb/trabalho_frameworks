<?php

namespace App\Actions\Imports;

use App\Models\RegioesImediatas;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarRegioesImediatas
{
    private $regioesImediatas;
    private $apiIbgeLocalidadesUrl;

    public function __construct(RegioesImediatas $regioesImediatas)
    {
        $this->regioesImediatas = $regioesImediatas;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
    }

    public function executar()
    {
        try {
            $url = $this->apiIbgeLocalidadesUrl . '/regioes-imediatas';
            $data = Http::timeout(300)->retry(3, 1000)->get($url);

            $dados = json_decode(Http::get($url)->body(), true);

            if ($data->status() === 200) :
                foreach ($dados as $dado) :
                    $id = $dado['id'];
                    $nome = $dado['nome'];
                    $regiao_intermediaria_id = $dado['regiao-intermediaria']['id'];
                    $estado_id = $dado['regiao-intermediaria']['UF']['id'];
                    $regiao_id = $dado['regiao-intermediaria']['UF']['regiao']['id'];
                    $retorno = $this->regioesImediatas::updateOrCreate(
                        [
                            'id' => $id
                        ],
                        [
                            'nome' => $nome,
                            'regiao_intermediaria_id' => $regiao_intermediaria_id,
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
