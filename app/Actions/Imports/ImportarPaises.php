<?php

namespace App\Actions\Imports;

use App\Models\Pais;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarPaises
{
    private $pais;
    private $apiIbgeLocalidadesUrl;
    private $pagina;

    public function __construct(Pais $pais)
    {
        $this->pais = $pais;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
        $this->pagina = '/paises';
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
                        'iso_alpha_2' => $item['id']['ISO-ALPHA-2'],
                        'iso_alpha_3' => $item['id']['ISO-ALPHA-3'],
                        'nome' => $item['nome'],
                        'regiao_intermediaria_m49' => $item['regiao-intermediaria']['id']['M49'] ?? null,
                        'regiao_intermediaria' => $item['regiao-intermediaria']['nome'] ?? null,
                        'sub_regiao_m49' => $item['sub-regiao']['id']['M49'],
                        'sub_regiao' => $item['sub-regiao']['nome'],
                        'regiao_m49' => $item['sub-regiao']['regiao']['id']['M49'],
                        'regiao' => $item['sub-regiao']['regiao']['nome'],
                    ];
                    $this->pais::updateOrCreate(
                        ['m49' => $item['id']['M49']],
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
