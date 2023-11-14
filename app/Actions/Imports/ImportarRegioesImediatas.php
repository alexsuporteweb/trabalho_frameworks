<?php

namespace App\Actions\Imports;

use App\Models\RegiaoImediata;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarRegioesImediatas
{
    private $regiaoImediata;
    private $apiIbgeLocalidadesUrl;
    private $pagina;

    public function __construct(RegiaoImediata $regiaoImediata)
    {
        $this->regiaoImediata = $regiaoImediata;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
        $this->pagina = '/regioes-imediatas';
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
                        'regiao_intermediaria_id' => $item['regiao-intermediaria']['id'],
                        'estado_id' => $item['regiao-intermediaria']['UF']['id'],
                        'regiao_id' => $item['regiao-intermediaria']['UF']['regiao']['id'],
                    ];

                    $this->regiaoImediata::updateOrCreate(
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
