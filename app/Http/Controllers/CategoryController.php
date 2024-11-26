<?php

namespace App\Http\Controllers;

use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController
{
    private $service;

    private $repository;

    public function __construct(CategoryService $service, CategoryRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $categories = $this->repository->getAllByEnterpriseWithDefaults($enterpriseId);

            return response()->json(['categories' => $categories], 200);
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

            if ($category) {
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
            $category = $this->service->update($request);

            if ($category) {
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

            if ($category) {
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

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $category = $this->service->delete($request, $id);

            if ($category['data']) {
                DB::commit();

                return response()->json(['message' => $category['message']], 200);
            }

            throw new \Exception('Falha ao deletar categoria');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar categoria: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
