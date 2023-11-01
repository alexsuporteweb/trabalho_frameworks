<?php

namespace App\Actions\Imports;

use App\Models\Paises;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarPaises
{
    private $paises;
    private $apiIbgeLocalidadesUrl;

    public function __construct(Paises $paises)
    {
        $this->paises = $paises;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
    }

    public function executar()
    {
        DB::beginTransaction();
        try {
            $dados = json_decode(
                Http::get(
                    $this->apiIbgeLocalidadesUrl . '/paises'
                )->body(),
                true
            );

            if ($dados) {
                foreach ($dados as $dado) {
                    $m49 = $dado['id']['M49'];
                    $iso_alpha_2 = $dado['id']['ISO-ALPHA-2'];
                    $iso_alpha_3 = $dado['id']['ISO-ALPHA-3'];
                    $nome = $dado['nome'];
                    $regiao_intermediaria_m49 = $dado['regiao-intermediaria']['id']['M49'] ?? null;
                    $regiao_intermediaria = $dado['regiao-intermediaria']['nome'] ?? null;
                    $sub_regiao_m49 = $dado['sub-regiao']['id']['M49'];
                    $sub_regiao = $dado['sub-regiao']['nome'];
                    $regiao_m49 = $dado['sub-regiao']['regiao']['id']['M49'];
                    $regiao = $dado['sub-regiao']['regiao']['nome'];
                    $retorno = $this->paises::updateOrCreate(
                        [
                            'm49' => $m49,
                        ],
                        [
                            'iso_alpha_2' => $iso_alpha_2,
                            'iso_alpha_3' => $iso_alpha_3,
                            'nome' => $nome,
                            'regiao_intermediaria_m49' => $regiao_intermediaria_m49 ?? null,
                            'regiao_intermediaria' => $regiao_intermediaria ?? null,
                            'sub_regiao_m49' => $sub_regiao_m49,
                            'sub_regiao' => $sub_regiao,
                            'regiao_m49' => $regiao_m49,
                            'regiao' => $regiao,
                        ]
                    );
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Erro durante consulta de API', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }
}
