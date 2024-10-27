<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function login(Request $request)
    {
        try {
            $user = $this->service->login($request);

            $token = $user->createToken('my-app-token')->plainTextToken;

            return response()->json(['user' => $user, 'token' => $token], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao logar com usuário: '.$e->getMessage());

            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->service->create($request);

            if ($user) {
                $token = $user->createToken('my-app-token')->plainTextToken;

                DB::commit();

                return response()->json(['user' => $user, 'token' => $token, 'message' => 'Cadastro realizado com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar usuário');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar usuário: '.$e->getMessage());

            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }
}
