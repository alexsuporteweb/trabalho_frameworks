<?php

namespace App\Actions\Imports;

use App\Models\Municipio;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarMunicipios
{
    private $municipio;
    private $apiIbgeLocalidadesUrl;
    private $pagina;

    public function __construct(Municipio $municipio)
    {
        $this->municipio = $municipio;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
        $this->pagina = '/municipios';
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
                        'microrregiao_id' => $item['microrregiao']['id'],
                        'mesorregiao_id' => $item['microrregiao']['mesorregiao']['id'],
                        'estado_id' => $item['microrregiao']['mesorregiao']['UF']['id'],
                        'regiao_id' => $item['microrregiao']['mesorregiao']['UF']['regiao']['id'],
                        'regiao_imediata_id' => $item['regiao-imediata']['id'],
                        'regiao_intermediaria_id' => $item['regiao-imediata']['regiao-intermediaria']['id'],
                    ];

                    $this->municipio::updateOrCreate(
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
