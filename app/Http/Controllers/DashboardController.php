<?php

namespace App\Http\Controllers;

use App\Repositories\DashboardRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController
{
    private $repository;

    public function __construct(DashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $dashboard = $this->repository->mountDashboard($enterpriseId);

            return response()->json([
                'months_years' => $dashboard['months_years'],
                'categories_dashboard' => $dashboard['categories_dashboard'],
                'movements_dashboard' => $dashboard['movements_dashboard'],
                'users_dashboard' => $dashboard['users_dashboard'],
                'schedulings_dashboard' => $dashboard['schedulings_dashboard'],
                'accounts_dashboard' => $dashboard['accounts_dashboard'],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar informações para o dashboard: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
        }
    }
}
