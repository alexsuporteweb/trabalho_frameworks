<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\Municipios;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class MunicipioController extends Controller
{
    private $municipios;

    public function __construct(Municipios $municipios)
    {
        $this->municipios = $municipios;
    }

    public function index()
    {
        try {
            $municipios =  $this->municipios::all();
            if ($municipios->isEmpty()) {
                return response()->json(['message' => 'Listagem vazia!'], 200);
            }

            return response()->json($municipios, 200);
        } catch (\Throwable $th) {
            Log::error('Erro durante a execução', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $data = $request->all();

            $this->validate($request, [
                'nome' => 'required',
            ], ['nome.required' => 'Campo nome é obrigatorio.',]);

            $municipio =  $this->municipios::find($id);
            if (!$municipio) {
                return response()->json(['message' => 'Registro não encontrado!'], 404);
            }

            $municipio->update($data);

            return response()->json(['message' => "Registro: {$municipio->id} - {$municipio->nome}, atualizado com sucesso"], 200);
        } catch (\Illuminate\Validation\ValidationException $exception) {

            $errorMessages = [];
            foreach ($exception->errors() as $field => $errors) {
                $errorMessages[$field] = $errors[0];
            }
            return response()->json(['errors' => $errorMessages], 400);
        } catch (\Throwable $th) {
            Log::error('Erro durante a execução', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }

    public function destroy(string $id)
    {
        try {
            $municipio =  $this->municipios::find($id);

            if ($municipio === null) {
                return response()->json(['message' => 'Registro não encontrado!'], 404);
            }

            $municipio->delete();
            return response()->json(['message' => "Registro: {$municipio->id} - {$municipio->nome}, removido com sucesso!"], 200);
        } catch (\Throwable $th) {
            Log::error('Erro durante a execução', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }
}
