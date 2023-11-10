<?php

namespace App\Actions\Imports;

use App\Models\Classes;
use App\Models\Divisoes;
use App\Models\Grupos;
use App\Models\Secoes;
use App\Models\Subclasses;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarSubclasses
{
    private $subClasses;
    private $apiIbgeCnaeUrl;

    public function __construct(Subclasses $subClasses)
    {
        $this->subClasses = $subClasses;
        $this->apiIbgeCnaeUrl = env('API_IBGE_CNAE_URL');
    }

    public function executar()
    {
        try {
            $url = $this->apiIbgeCnaeUrl . '/subclasses';
            $data = Http::timeout(300)->retry(3, 1000)->get($url);

            $dados = json_decode(Http::get($url)->body(), true);

            if ($data->status() === 200) :
                foreach ($dados as $dado) :
                    $codigo = $dado['id'];
                    $descricao = $dado['descricao'];
                    $classe_id = Classes::where('codigo', $dado['classe']['id'])->first()->id;
                    $grupo_id = Grupos::where('codigo', $dado['classe']['grupo']['id'])->first()->id;
                    $divisao_id = Divisoes::where('codigo', $dado['classe']['grupo']['divisao']['id'])->first()->id;
                    $secao_id = Secoes::where('codigo', $dado['classe']['grupo']['divisao']['secao']['id'])->first()->id;

                    $atividades = '';
                    for ($i = 0; $i < count($dado['atividades']); $i++) :
                        $atividades .= "{$dado['atividades'][$i]}\r\n";
                    endfor;

                    $observacoes = '';
                    for ($i = 0; $i < count($dado['observacoes']); $i++) :
                        $observacoes .= "{$dado['observacoes'][$i]}\r\n";
                    endfor;

                    $retorno = $this->subClasses::updateOrCreate(
                        [
                            'codigo' => $codigo
                        ],
                        [
                            'atividades' => $atividades,
                            'observacoes' => $observacoes,
                            'descricao' => $descricao,
                            'classe_id' => $classe_id,
                            'grupo_id' => $grupo_id,
                            'divisao_id' => $divisao_id,
                            'secao_id' => $secao_id,
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
