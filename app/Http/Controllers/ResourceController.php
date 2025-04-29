<?php

namespace App\Http\Controllers;

use App\Helpers\RegisterHelper;
use App\Repositories\CategoryRepository;
use App\Repositories\EnterpriseRepository;
use App\Rules\AlertRule;
use App\Services\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResourceController
{
    private $service;

    private $repository;

    private $enterpriseRepository;

    private $rule;

    public function __construct(AlertService $service, CategoryRepository $repository, AlertRule $rule, EnterpriseRepository $enterpriseRepository)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
        $this->enterpriseRepository = $enterpriseRepository;
    }

    public function index($id)
    {
        try {
            $categories = $this->repository->getAllByEnterpriseWithDefaults($id);

            return response()->json(['categories' => $categories], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as categorias: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


}
