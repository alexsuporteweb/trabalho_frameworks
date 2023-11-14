<?php

namespace App\Actions\Imports;

use App\Models\Classe;
use App\Models\Divisao;
use App\Models\Grupo;
use App\Models\Secao;
use App\Models\Subclasse;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarSubclasses
{
    private $classe;
    private $divisao;
    private $grupo;
    private $secao;
    private $subClasse;
    private $apiIbgeCnaeUrl;
    private $pagina;

    public function __construct(
        Classe $classe,
        Divisao $divisao,
        Grupo $grupo,
        Secao $secao,
        Subclasse $subClasse
    ) {
        $this->classe = $classe;
        $this->divisao = $divisao;
        $this->grupo = $grupo;
        $this->secao = $secao;
        $this->subClasse = $subClasse;
        $this->apiIbgeCnaeUrl = env('API_IBGE_CNAE_URL');
        $this->pagina = '/subclasses';
    }

    public function executar()
    {
        $start_time = microtime(true);
        try {
            $uri = $this->apiIbgeCnaeUrl . $this->pagina;
            $response = Http::timeout(300)->retry(3, 1000)->get($uri);

            if ($response->successful()) {
                $data = json_decode($response->body(), true);

                foreach ($data as $dado) :
                    $codigo = $dado['id'];
                    $descricao = $dado['descricao'];
                    $classe_id = $this->classe::where('codigo', $dado['classe']['id'])->first()->id;
                    $grupo_id = $this->grupo::where('codigo', $dado['classe']['grupo']['id'])->first()->id;
                    $divisao_id = $this->divisao::where('codigo', $dado['classe']['grupo']['divisao']['id'])->first()->id;
                    $secao_id = $this->secao::where('codigo', $dado['classe']['grupo']['divisao']['secao']['id'])->first()->id;

                    $atividades = '';
                    for ($i = 0; $i < count($dado['atividades']); $i++) :
                        $atividades .= "{$dado['atividades'][$i]}\r\n";
                    endfor;

                    $observacoes = '';
                    for ($i = 0; $i < count($dado['observacoes']); $i++) :
                        $observacoes .= "{$dado['observacoes'][$i]}\r\n";
                    endfor;

                    $retorno = $this->subClasse::updateOrCreate(
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
            } else {
                return response()->json(['message' => 'Erro na solicitação. Status code: ' . $response->status()], 400);
            }
        } catch (\Throwable $th) {
            Log::error('Erro durante consulta de API', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        } finally {
            $end_time = microtime(true);
            $execution_time = round($end_time - $start_time, 2);
        }
        echo 'Seeding completed in ' . $execution_time . ' seconds.' . PHP_EOL;
    }
}
