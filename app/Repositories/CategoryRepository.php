<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryRepository
{
    protected $model;

    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getEnterpriseCategoryByCounter($enterpriseId, $type, $classification)
    {
        $subquery = $this->model->newQuery()
            ->selectRaw('FIRST_VALUE(id) OVER (PARTITION BY name, type ORDER BY created_at) as first_id')
            ->whereHas('enterprise', function($q) use ($enterpriseId) {
                $q->where('counter_enterprise_id', $enterpriseId);
            })
            ->where('default', 1);

        $ids = DB::table(DB::raw("({$subquery->toSql()}) as sub"))
            ->mergeBindings($subquery->getQuery())
            ->pluck('first_id');

        $nonDefaultQuery = $this->model->where('default', '!=', 1)
            ->whereHas('enterprise', function($q) use ($enterpriseId) {
                $q->where('counter_enterprise_id', $enterpriseId);
            });

        if(in_array($type, ['entrada', 'saida'])){
            $nonDefaultQuery->where('type', $type);
        }
        if(in_array($classification, ['1', '0'])){
            $nonDefaultQuery->where('default', (int) $classification);
        }

        $nonDefaultIds = $nonDefaultQuery->pluck('id');

        $allIds = $ids->merge($nonDefaultIds);

        $query = $this->model->whereIn('id', $allIds)
            ->with(['enterprise' => function($query) use ($enterpriseId) {
                $query->where('counter_enterprise_id', $enterpriseId);
            }]);

        if(in_array($type, ['entrada', 'saida'])){
            $query->where('type', $type);
        }
        if(in_array($classification, ['1', '0'])){
            $query->where('default', (int) $classification);
        }

        return $query->get();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getAllByEnterpriseWithRelationsWithParams($request)
    {
        $createdByMe = $request->has('createdByMe') ? filter_var($request->query('createdByMe'), FILTER_VALIDATE_BOOLEAN) : null;
        $defaultSystem = $request->has('defaultSystem') ? filter_var($request->query('defaultSystem'), FILTER_VALIDATE_BOOLEAN) : null;

        $query = $this->model->with(['alert']);
        $query->where('enterprise_id', $request->user()->view_enterprise_id);

        if (! is_null($createdByMe) && $createdByMe) {
            $query->where('default', 0);
        }

        if (! is_null($defaultSystem) && $defaultSystem) {
            $query->where('default', 1);
        }

        return $query->get();
    }

    public function getAllByEnterpriseWithDefaults($enterpriseId, $type = null)
    {
        return $this->model->where('enterprise_id', $enterpriseId)
            ->when(in_array($type, ['entrada', 'saída']), function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getAllByEnterpriseWithDefaultsOnlyActive($enterpriseId, $type = null)
    {
        return $this->model->where('active', 1)
            ->where('enterprise_id', $enterpriseId)
            ->when(in_array($type, ['entrada', 'saída']), function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByName($name, $type)
    {
        return $this->model->where('name', $name)->where('type', $type)->first();
    }

    public function findByNameAndType($name, $type, $enterpriseId)
    {
        return $this->model
            ->where(DB::raw('LOWER(name)'), '=', strtolower($name))
            ->where('type', $type)
            ->where('enterprise_id', $enterpriseId)
            ->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function updateAllDefaultsWithName(array $data)
    {
        if (!isset($data['name'])) {
            throw new \InvalidArgumentException("O campo 'name' é obrigatório para a atualização");
        }

        $categories = Category::where('default', 1)
                            ->where('name', $data['name'])
                            ->get();

        if ($categories->isEmpty()) {
            return null;
        }

        if($categories){
            foreach ($categories as $category) {
                $category->update($data);
            }
            return $categories;
        }
        return null;
    }


    public function update($id, array $data)
    {
        $category = $this->findById($id);
        if ($category) {
            $category->update($data);

            return $category;
        }

        return null;
    }

    public function removeAlert($enterpriseId)
    {
        $categories = $this->getAllByEnterprise($enterpriseId);

        if ($categories->isNotEmpty()) {
            foreach ($categories as $category) {
                $category->alert = null;
                $category->save();
            }
        }
    }

    public function delete($id)
    {
        $category = $this->findById($id);
        if ($category) {
            return $category->delete();
        }

        return false;
    }
}
