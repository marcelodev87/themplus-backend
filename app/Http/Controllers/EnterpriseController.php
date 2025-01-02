<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Helpers\RegisterHelper;
use App\Http\Resources\OfficeResource;
use App\Repositories\EnterpriseRepository;
use App\Services\EnterpriseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnterpriseController
{
    private $service;

    private $repository;

    public function __construct(EnterpriseService $service, EnterpriseRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function indexOffices(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $offices = $this->repository->getAllOfficesByEnterprise($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['offices' => OfficeResource::collection($offices), 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as filiais: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function storeOffice(Request $request)
    {
        try {
            DB::beginTransaction();
            $office = $this->service->createOffice($request);

            if ($office) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $offices = $this->repository->getAllOfficesByEnterprise($enterpriseId);

                return response()->json(['offices' => $offices, 'message' => 'Filial cadastrada com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar filial');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar filial: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $enterprise = $this->repository->findById($enterpriseId);

            return response()->json(['enterprise' => $enterprise], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados da organização: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function search(Request $request, $text)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $enterprises = $this->repository->searchEnterprise($enterpriseId, $text);

            return response()->json(['enterprise' => $enterprise], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados da organização: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $enterprise = $this->service->update($request);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'enterprise',
                "{$request->user()->enterprise->name}"
            );

            if ($enterprise && $register) {
                DB::commit();

                return response()->json(['enterprise' => $enterprise, 'message' => 'Organização atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar organização');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar organização: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        //
    }
}
