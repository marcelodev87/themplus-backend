<?php

namespace App\Http\Controllers;

use App\Rules\FeedbackRule;
use App\Services\FeedbackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeedbackController
{
    private $rule;

    private $service;

    public function __construct(
        FeedbackRule $rule,
        FeedbackService $service
    ) {
        $this->rule = $rule;
        $this->service = $service;
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

            $this->service->saveFeedbackBySettings($request);

            DB::commit();

            return response()->json(['message' => 'Mensagem enviada com sucesso'], 201);
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
