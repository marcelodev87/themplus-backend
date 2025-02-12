<?php

namespace App\Http\Controllers;

use App\Repositories\FeedbackRepository;
use App\Rules\FeedbackRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeedbackController
{
    private $repository;

    private $rule;

    public function __construct(FeedbackRepository $repository, FeedbackRule $rule)
    {
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        // Definir como vai ser para ler as sugestoes
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $this->rule->create($request);
            $data = [
                'user_id' => $request->user()->id,
                'enterprise_id' => $request->user()->enterprise_id,
                'message' => $request->input('message'),
            ];
            $feedback = $this->repository->create($data);

            if ($feedback) {
                DB::commit();

                return response()->json(['message' => 'Mensagem enviada com sucesso'], 201);
            }

            throw new \Exception('Falha ao enviar mensagem');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao enviar mensagem: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        // Definir se vai ser possivel deletar
    }
}
