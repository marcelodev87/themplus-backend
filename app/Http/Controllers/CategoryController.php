<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Helpers\NotificationsHelper;
use App\Helpers\RegisterHelper;
use App\Repositories\CategoryRepository;
use App\Rules\CategoryRule;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController
{
    private $service;

    private $repository;

    private $rule;

    public function __construct(CategoryService $service, CategoryRepository $repository, CategoryRule $rule)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->view_enterprise_id;
            $categories = $this->repository->getAllByEnterpriseWithDefaults($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);
            $notifications = NotificationsHelper::getNoRead($request->user()->id);

            return response()->json(['categories' => $categories, 'filled_data' => $filledData, 'notifications' => $notifications], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as categorias: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function filterCategories(Request $request)
    {
        try {
            $categories = $this->repository->getAllByEnterpriseWithRelationsWithParams($request);

            return response()->json(['categories' => $categories], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar categorias com base nos filtros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $category = $this->service->create($request);
            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'created',
                'category',
                "$category->name|$category->type"
            );

            if ($category && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $categories = $this->repository->getAllByEnterpriseWithDefaults($enterpriseId);

                return response()->json(['categories' => $categories, 'message' => 'Categoria cadastrada com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar categoria');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar categoria: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $categoryData = $this->repository->findById($request->input('id'));
            $category = $this->service->update($request);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'category',
                $categoryData->name.'|'.$categoryData->type
            );

            if ($category && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $categories = $this->repository->getAllByEnterpriseWithDefaults($enterpriseId);

                return response()->json(['categories' => $categories, 'message' => 'Categoria atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar categoria');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar categoria: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function active(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $category = $this->service->updateActive($id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'reactivated',
                'category',
                "$category->name|$category->type"
            );

            if ($category && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $categories = $this->repository->getAllByEnterpriseWithDefaults($enterpriseId);

                return response()->json(['categories' => $categories, 'message' => 'Categoria reativada com sucesso'], 200);
            }

            throw new \Exception('Falha ao reativar categoria');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao reativar categoria: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $categoryData = $this->repository->findById($id);
            $category = $this->service->delete($id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                $category['inactivated'] ? 'inactivated' : 'deleted',
                'category',
                $categoryData->name.'|'.$categoryData->type
            );

            if ($category['data'] && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $categories = $this->repository->getAllByEnterpriseWithDefaults($enterpriseId);

                return response()->json(['categories' => $categories, 'message' => $category['message']], 200);
            }

            throw new \Exception('Falha ao deletar categoria');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar categoria: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
