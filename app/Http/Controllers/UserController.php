<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController
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

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
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

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
        }
    }

    public function updateData(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $this->service->updateData($request);

            if ($user) {
                DB::commit();

                return response()->json(['user' => $user, 'message' => 'Seus dados foram atualizados com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar seus dados');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar dados do usuário atual: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $this->service->updatePassword($request);

            if ($user) {
                DB::commit();

                return response()->json(['user' => $user, 'message' => 'Sua senha foi atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar sua senha');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar senha do usuário atual: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
        }
    }
}
