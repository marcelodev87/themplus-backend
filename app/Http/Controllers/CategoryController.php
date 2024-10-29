<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    private $service;
    private $repository;

    public function __construct(CategoryService $service,  CategoryRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }
    public function index()
    {
        return 'Ola mundo';
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

            return response()->json(['message' => 'Houve erro: '.$e->getMessage() ], 500);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
