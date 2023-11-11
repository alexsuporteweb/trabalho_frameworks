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
    public function index()
    {
        try {
            $municipios = Municipios::all();
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

            $municipio = Municipios::find($id);
            if (!$municipio) {
                return response()->json(['message' => 'Registro não encontrado!'], 404);
            }

            $municipio->update($data);

            return response()->json(['message' => "Registro: {$municipio->id} - {$municipio->id}, atualizado com sucesso"], 200);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $errorMessage = $exception->errors()['nome'][0];

            if ($errorMessage) {
                return response()->json(['message' => $errorMessage], 400);
            }
            throw $exception;
        } catch (\Throwable $th) {
            Log::error('Erro durante a execução', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }

    public function destroy(string $id)
    {
        try {
            $municipio = Municipios::find($id);

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
