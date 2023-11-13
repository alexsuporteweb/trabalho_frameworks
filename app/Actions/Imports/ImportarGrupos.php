<?php

namespace App\Actions\Imports;

use App\Models\Divisao;
use App\Models\Grupo;
use App\Models\Secao;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarGrupo
{
    private $divisao;
    private $grupo;
    private $secao;
    private $apiIbgeCnaeUrl;
    private $pagina;

    public function __construct(Divisao $divisao, Grupo $grupo, Secao $secao)
    {
        $this->grupo = $grupo;
        $this->divisao = $divisao;
        $this->secao = $secao;
        $this->apiIbgeCnaeUrl = env('API_IBGE_CNAE_URL');
        $this->pagina = '/grupo';
    }

    public function executar()
    {
        $start_time = microtime(true);
        try {
            $uri = $this->apiIbgeCnaeUrl . $this->pagina;
            $response = Http::timeout(300)->retry(3, 1000)->get($uri);

            if ($response->successful()) {
                $data = json_decode($response->body(), true);

                foreach ($data as $item) {
                    $dados = [
                        'divisao_id' => $this->divisao::where('codigo', $item['divisao']['id'])->first()->id,
                        'secao_id' => $this->secao::where('codigo', $item['divisao']['secao']['id'])->first()->id,
                        'descricao' => $item['descricao'],
                    ];

                    $this->grupo::updateOrCreate(
                        ['codigo' => $item['id']],
                        $dados
                    );
                }
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
