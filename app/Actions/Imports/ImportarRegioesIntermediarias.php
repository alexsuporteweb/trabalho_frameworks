<?php

namespace App\Actions\Imports;

use App\Models\RegiaoIntermediaria;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarRegioesIntermediarias
{
    private $regiaoIntermediaria;
    private $apiIbgeLocalidadesUrl;
    private $pagina;

    public function __construct(RegiaoIntermediaria $regiaoIntermediaria)
    {
        $this->regiaoIntermediaria = $regiaoIntermediaria;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
        $this->pagina = '/regioes-intermediarias';
    }

    public function executar()
    {
        $start_time = microtime(true);
        try {
            $uri = $this->apiIbgeLocalidadesUrl . $this->pagina;
            $response = Http::timeout(300)->retry(3, 1000)->get($uri);

            if ($response->successful()) {
                $data = json_decode($response->body(), true);

                foreach ($data as $item) {
                    $dados = [
                        'nome' => $item['nome'],
                        'estado_id' => $item['UF']['id'],
                        'regiao_id' => $item['UF']['regiao']['id'],
                    ];

                    $this->regiaoIntermediaria::updateOrCreate(
                        ['id' => $item['id']],
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
