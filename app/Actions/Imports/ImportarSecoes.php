<?php

namespace App\Actions\Imports;

use App\Models\Secao;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarSecoes
{
    private $secao;
    private $apiIbgeCnaeUrl;
    private $pagina;

    public function __construct(Secao $secao)
    {
        $this->secao = $secao;
        $this->apiIbgeCnaeUrl = env('API_IBGE_CNAE_URL');
        $this->pagina = '/secoes';
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
                        'descricao' => $item['descricao']
                    ];

                    $observacoes = '';
                    for ($i = 0; $i < count($item['observacoes']); $i++) :
                        $observacoes .= "{$item['observacoes'][$i]}\r\n";
                    endfor;

                    $this->secao::updateOrCreate(
                        [
                            'codigo' => $item['id']
                        ],
                        [
                            'descricao' => $dados['descricao'],
                            'observacoes' => $observacoes,
                        ]
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
