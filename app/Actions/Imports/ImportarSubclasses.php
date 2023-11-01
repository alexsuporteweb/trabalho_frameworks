<?php

namespace App\Actions\Imports;

use App\Models\Classes;
use App\Models\Divisoes;
use App\Models\Grupos;
use App\Models\Secoes;
use App\Models\Subclasses;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarSubclasses
{
    private $subClasses;
    private $apiIbgeCnaeUrl;

    public function __construct(Subclasses $subClasses)
    {
        $this->subClasses = $subClasses;
        $this->apiIbgeCnaeUrl = env('API_IBGE_CNAE_URL');
    }

    public function executar()
    {
        // DB::beginTransaction();
        // try {
        // $dados = json_decode(
        //     Http::timeout(120)->get(
        //         $this->apiIbgeCnaeUrl . '/subclasses'
        //     )->body()
        // );


        $response = Http::timeout(120)->get($this->apiIbgeCnaeUrl . '/subclasses');
        $data = $response->json();

        dd($data);

        if ($dados) :
            foreach ($dados as $dado) :
                dd($dado);
                $codigo = $dado->id;
                $descricao = $dado->descricao;
                $atividades = $dado->atividades;
                $classe_id = Classes::where('codigo', $dado->classe->id)->first()->id;
                $grupo_id = Grupos::where('codigo', $dado->classe->grupo->id)->first()->id;
                $divisao_id = Divisoes::where('codigo', $dado->classe->grupo->divisao->id)->first()->id;
                $secao_id = Secoes::where('codigo', $dado->classe->grupo->divisao->secao->id)->first()->id;

                $observacoes = '';
                for ($i = 0; $i < count($dado->observacoes); $i++) :
                    $observacoes .= "{$dado->observacoes[$i]}\r\n";
                endfor;

                $retorno = $this->subClasses::create([
                    'codigo' => $codigo,
                    'descricao' => $descricao,
                    'atividades' => $atividades,
                    'classe_id' => $classe_id,
                    'grupo_id' => $grupo_id,
                    'divisao_id' => $divisao_id,
                    'secao_id' => $secao_id,
                    'observacoes' => $observacoes,
                ]);

            // $retorno = $this->subClasses::updateOrCreate(
            //     [
            //         'codigo' => $codigo
            //     ],
            //     [
            //         'descricao' => $descricao,
            //         'atividades' => $atividades,
            //         'classe_id' => $classe_id,
            //         'grupo_id' => $grupo_id,
            //         'divisao_id' => $divisao_id,
            //         'secao_id' => $secao_id,
            //         'observacoes' => $observacoes,
            //     ]
            // );
            endforeach;
        endif;
        //     DB::commit();
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     Log::error('Erro durante consulta de API', ['erro' => $th->getMessage()]);
        //     throw new Exception($th->getMessage(), 1);
        // }
    }
}
