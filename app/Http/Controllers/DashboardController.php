<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
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

    public function index(Request $request, $date)
    {
        try {
            $enterpriseId = $request->user()->view_enterprise_id;

            $dashboard = $this->repository->mountDashboard($enterpriseId, $date);
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json([
                'months_years' => $dashboard['months_years'],
                'categories_dashboard' => $dashboard['categories_dashboard'],
                'movements_dashboard' => $dashboard['movements_dashboard'],
                'users_dashboard' => $dashboard['users_dashboard'],
                'schedulings_dashboard' => $dashboard['schedulings_dashboard'],
                'accounts_dashboard' => $dashboard['accounts_dashboard'],
                'filled_data' => $filledData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar informações para o dashboard: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
