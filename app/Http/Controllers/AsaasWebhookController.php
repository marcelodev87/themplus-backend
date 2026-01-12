<?php

namespace App\Http\Controllers;

use App\Services\AsaasWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AsaasWebhookController
{
    private $service;

    public function __construct(AsaasWebhookService $service)
    {
        $this->service = $service;
    }

    public function webhook(Request $request)
    {
        try {
            DB::beginTransaction();

            $status = $this->service->checkWebhook($request);

            if ($status) {
                DB::commit();

                return response()->json([200]);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao buscar ao receber webhook: '.$e->getMessage());

            return response()->json(['message' => 'Erro ao receber webhook'], 500);
        }
    }
}
