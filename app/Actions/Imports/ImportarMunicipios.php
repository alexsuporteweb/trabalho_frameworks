<?php

namespace App\Actions\Imports;

use App\Models\Municipios;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarMunicipios
{
    private $municipios;
    private $apiIbgeLocalidadesUrl;

    public function __construct(Municipios $municipios)
    {
        $this->municipios = $municipios;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
    }

    public function executar()
    {
        DB::beginTransaction();
        try {
            $dados = json_decode(
                Http::get(
                    $this->apiIbgeLocalidadesUrl . '/municipios'
                )->body()
            );

            if ($dados) :
                foreach ($dados as $dado) :
                    $id = $dado->id;
                    $nome = $dado->nome;
                    $microregiao_id = $dado->microrregiao->id;
                    $mesorregiao_id = $dado->microrregiao->mesorregiao->id;
                    $estado_id = $dado->microrregiao->mesorregiao->UF->id;
                    $regiao_id = $dado->microrregiao->mesorregiao->UF->regiao->id;
                    $regiao_imediata_id = $dado->{"regiao-imediata"}->id;
                    $regiao_intermediaria_id = $dado->{"regiao-imediata"}->{"regiao-intermediaria"}->id;
                    $retorno = $this->municipios::updateOrCreate(
                        [
                            'id' => $id
                        ],
                        [
                            'nome' => $nome,
                            'microregiao_id' => $microregiao_id,
                            'mesorregiao_id' => $mesorregiao_id,
                            'estado_id' => $estado_id,
                            'regiao_id' => $regiao_id,
                            'regiao_imediata_id' => $regiao_imediata_id,
                            'regiao_intermediaria_id' => $regiao_intermediaria_id,
                        ]
                    );
                endforeach;
            endif;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Erro durante consulta de API', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }
}
