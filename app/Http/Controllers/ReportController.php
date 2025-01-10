<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController
{
    private $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request, $id)
    {
        try {
            $data = $this->service->index($request, $id);

            return response()->json([
                'reports' => $data['reports'],
                'client_name' => $data['client_name'],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar relatório do cliente: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
