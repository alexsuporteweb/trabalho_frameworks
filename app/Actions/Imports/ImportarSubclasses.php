<?php

namespace App\Actions\Imports;

use App\Models\Classes;
use App\Models\Divisao;
use App\Models\Grupo;
use App\Models\Secoes;
use App\Models\Subclasses;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarSubclasses
{
    private $classes;
    private $divisao;
    private $grupo;
    private $secoes;
    private $subClasses;
    private $apiIbgeCnaeUrl;
    private $pagina;

    public function __construct(
        Classes $classes,
        Divisao $divisao,
        Grupo $grupo,
        Secoes $secoes,
        Subclasses $subClasses
    ) {
        $this->classes = $classes;
        $this->divisao = $divisao;
        $this->grupo = $grupo;
        $this->secoes = $secoes;
        $this->subClasses = $subClasses;
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
                    $classe_id = $this->classes::where('codigo', $dado['classe']['id'])->first()->id;
                    $grupo_id = $this->grupo::where('codigo', $dado['classe']['grupo']['id'])->first()->id;
                    $divisao_id = $this->divisao::where('codigo', $dado['classe']['grupo']['divisao']['id'])->first()->id;
                    $secao_id = $this->secoes::where('codigo', $dado['classe']['grupo']['divisao']['secao']['id'])->first()->id;

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
