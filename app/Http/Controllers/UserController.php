<?php

namespace App\Http\Controllers;

use App\Rules\UserRule;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController
{
    private $service;

    private $rule;

    public function __construct(UserService $service, UserRule $rule)
    {
        $this->service = $service;
        $this->rule = $rule;
    }

    public function login(Request $request)
    {
        try {
            $user = $this->service->login($request);

            $token = $user->createToken('my-app-token')->plainTextToken;

            return response()->json(['user' => $user, 'token' => $token], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao logar com usuário: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
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

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reset(Request $request)
    {
        try {
            $result = $this->service->reset($request);

            return response()->json(['message' => $result], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao solicitar redefinição de senha: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function verify(Request $request)
    {
        try {
            $result = $this->service->verify($request);

            return response()->json(['valid' => $result['valid'], 'message' => $result['message']], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao verificar código: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $this->service->resetPassword($request);

            if ($user) {
                DB::commit();

                return response()->json(['user' => $user, 'message' => 'Sua senha foi atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar sua senha');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar senha do usuário atual: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
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

            return response()->json(['message' => $e->getMessage()], 500);
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

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
