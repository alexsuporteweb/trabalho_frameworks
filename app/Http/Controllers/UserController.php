<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = User::all();
            if ($user->isEmpty()) {
                return response()->json(['message' => 'Listagem vazia!'], 200);
            }

            return response()->json($user, 200);
        } catch (\Throwable $th) {
            Log::error('Erro durante a execução', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json('create', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()->json('create', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::find($id);

            if ($user === null) {
                return response()->json(['message' => 'Registro não encontrado!'], 404);
            }

            return response()->json($user, 200);

            if (empty($id) || !is_numeric($id)) {

                return response()->json(['message' => 'Registro não encontrado!'], 400);
            }
        } catch (\Throwable $th) {
            Log::error('Erro durante a execução', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $user = User::find($id);

            if ($user === null) {
                return response()->json(['message' => 'Registro não encontrado!'], 404);
            }

            return response()->json($user, 200);

            if (empty($id) || !is_numeric($id)) {

                return response()->json(['message' => 'Registro não encontrado!'], 400);
            }
        } catch (\Throwable $th) {
            Log::error('Erro durante a execução', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = $request->all();

            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email',
                'admin' => 'required',
            ]);

            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'Registro não encontrado!'], 404);
            }

            $user->update($data);

            return response()->json(['message' => 'Registro atualizado com sucesso'], 200);
        } catch (\Throwable $th) {
            Log::error('Erro durante a execução', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::find($id);

            if ($user === null) {
                return response()->json(['message' => 'Registro não encontrado!'], 404);
            }

            $user->delete();
            return response()->json(['message' => 'Registro removido com sucesso!'], 200);
        } catch (\Throwable $th) {
            Log::error('Erro durante a execução', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }
}
