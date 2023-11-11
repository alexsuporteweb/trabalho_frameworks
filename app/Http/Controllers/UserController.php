<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth');
    }

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
        try {
            $data = $request->validate(
                [
                    'name' => 'required',
                    'email' => ['required', Rule::unique('users')->ignore($request->id)],
                    'password' => 'required',
                    'admin' => 'required',
                ],
                [
                    'name.required' => 'name é obrigatorio.',
                    'email.required' => 'email é obrigatorio.',
                    'email.unique' => 'O email já está em uso.',
                    'password.required' => 'password é obrigatorio.',
                    'admin.required' => 'admin é obrigatorio.',
                ]
            );

            $user = User::create(
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password']),
                    'admin' => $data['admin'],
                ]
            );

            if ($user) {
                return response()->json(['message' => 'Registro criado com sucesso'], 200);
            }

            return response()->json(['message' => 'Erro ao criar registro!'], 400);
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
                'email' => ['required', Rule::unique('users')->ignore($request->id)],
                'password' => 'required',
                'admin' => 'required',
            ], [
                'name.required' => 'name é obrigatorio.',
                'email.required' => 'email é obrigatorio.',
                'email.unique' => 'O email já está em uso.',
                'password.required' => 'password é obrigatorio.',
                'admin.required' => 'admin é obrigatorio.',
            ]);

            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'Registro não encontrado!'], 404);
            }

            $user->update($data);

            return response()->json(['message' => 'Registro atualizado com sucesso'], 200);
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
            return response()->json(['message' => "Registro: {$id}, removido com sucesso!"], 200);
        } catch (\Throwable $th) {
            Log::error('Erro durante a execução', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }

    public function messages()
    {
        return [
            'email.unique' => 'O email já está em uso.',
        ];
    }
}
