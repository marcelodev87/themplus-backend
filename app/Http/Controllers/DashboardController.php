<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Helpers\NotificationsHelper;
use App\Repositories\DashboardRepository;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
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
            $enterpriseId = $request->user()->view_enterprise_id;

            $dashboard = $this->repository->mountDashboard($enterpriseId, $request);
            $filledData = EnterpriseHelper::filledData($enterpriseId);
            $notifications = NotificationsHelper::getNoRead($request->user()->id);

            return response()->json([
                'months_years' => $dashboard['months_years'],
                'categories_movements_dashboard' => $dashboard['categories_movements_dashboard'],
                'categories_schedules_dashboard' => $dashboard['categories_schedules_dashboard'],
                'categories' => $dashboard['categories'],
                'movements_dashboard' => $dashboard['movements_dashboard'],
                'users_dashboard' => $dashboard['users_dashboard'],
                'schedulings_dashboard' => $dashboard['schedulings_dashboard'],
                'accounts_dashboard' => $dashboard['accounts_dashboard'],
                'general' => $dashboard['general'],
                'notifications' => $notifications,
                'filled_data' => $filledData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar informações para o dashboard: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $enterpriseId = $request->user()->view_enterprise_id;

            $dashboard = $this->repository->mountDashboard($enterpriseId, $request);

            $pdf = PDF::loadView('dashboard.pdf', [
                'months_years' => $dashboard['months_years'],
                'categories_movements_dashboard' => $dashboard['categories_movements_dashboard'],
                'categories_schedules_dashboard' => $dashboard['categories_schedules_dashboard'],
                'categories' => $dashboard['categories'],
                'movements_dashboard' => $dashboard['movements_dashboard'],
                'users_dashboard' => $dashboard['users_dashboard'],
                'schedulings_dashboard' => $dashboard['schedulings_dashboard'],
                'accounts_dashboard' => $dashboard['accounts_dashboard'],
                'general' => $dashboard['general'],
                'date' => $request->input('date'),
            ]);

            return $pdf->download('dashboard.pdf');
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF do dashboard: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
