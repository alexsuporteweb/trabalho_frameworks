<?php

namespace App\Actions\Imports;

use App\Models\Microrregiao;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarMicrorregioes
{
    private $microrregiao;
    private $apiIbgeLocalidadesUrl;
    private $pagina;

    public function __construct(Microrregiao $microregioes)
    {
        $this->microrregiao = $microregioes;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
        $this->pagina = '/microrregioes';
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
                        'mesorregiao_id' => $item['mesorregiao']['id'],
                        'estado_id' => $item['mesorregiao']['UF']['id'],
                        'regiao_id' => $item['mesorregiao']['UF']['regiao']['id'],
                    ];

                    $this->microrregiao::updateOrCreate(
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
