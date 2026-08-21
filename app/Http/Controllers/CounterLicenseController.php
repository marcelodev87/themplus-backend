<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationsHelper;
use App\Repositories\CounterClientLicenseRepository;
use App\Services\CounterLicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CounterLicenseController
{
    protected $service;

    protected $licenseRepository;

    public function __construct(CounterLicenseService $service, CounterClientLicenseRepository $licenseRepository)
    {
        $this->service = $service;
        $this->licenseRepository = $licenseRepository;
    }

    public function usage(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $usage = $this->service->getUsage($enterpriseId);
            $licenses = $this->licenseRepository->getActiveByCounter($enterpriseId);
            $notifications = NotificationsHelper::getNoRead($request->user()->id);

            return response()->json([
                'usage' => $usage,
                'licenses' => $licenses,
                'notifications' => $notifications,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar uso de licenças do contador: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function grant(Request $request, $clientEnterpriseId)
    {
        try {
            DB::beginTransaction();
            $enterpriseId = $request->user()->enterprise_id;
            $license = $this->service->grant($enterpriseId, $clientEnterpriseId);

            if ($license) {
                DB::commit();

                return response()->json([
                    'usage' => $this->service->getUsage($enterpriseId),
                    'message' => 'Assinatura Básica concedida com sucesso',
                ], 201);
            }

            throw new \Exception('Falha ao conceder assinatura Básica');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao conceder licença: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function revoke(Request $request, $clientEnterpriseId)
    {
        try {
            DB::beginTransaction();
            $enterpriseId = $request->user()->enterprise_id;
            $license = $this->service->revoke($enterpriseId, $clientEnterpriseId);

            if ($license) {
                DB::commit();

                return response()->json([
                    'usage' => $this->service->getUsage($enterpriseId),
                    'message' => 'Assinatura Básica removida com sucesso',
                ], 200);
            }

            throw new \Exception('Falha ao remover assinatura Básica');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao revogar licença: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
