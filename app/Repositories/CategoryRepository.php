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

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getAllByEnterpriseWithRelationsWithParams($request)
    {
        $createdByMe = $request->has('createdByMe') ? filter_var($request->query('createdByMe'), FILTER_VALIDATE_BOOLEAN) : null;
        $defaultSystem = $request->has('defaultSystem') ? filter_var($request->query('defaultSystem'), FILTER_VALIDATE_BOOLEAN) : null;

        $query = $this->model->with(['alert']);

        if (! is_null($createdByMe) && $createdByMe) {
            $query->where('enterprise_id', $request->user()->enterprise_id);
        }

        if (! is_null($defaultSystem) && $defaultSystem) {
            $query->where('enterprise_id', null);
        }

        return $query->get();
    }

    public function getAllByEnterpriseWithDefaults($enterpriseId, $type = null)
    {
        return $this->model->with('alert')
            ->where(function ($query) use ($enterpriseId) {
                $query->where('enterprise_id', $enterpriseId)
                    ->orWhere('enterprise_id', null);
            })
            ->when($type === 'entrada' || $type === 'saída', function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->get();
    }

    public function getAllByEnterpriseWithDefaultsOnlyActive($enterpriseId, $type = null)
    {
        return $this->model->with('alert')
            ->where('active', 1)
            ->where(function ($query) use ($enterpriseId) {
                $query->where('enterprise_id', $enterpriseId)
                    ->orWhere('enterprise_id', null);
            })
            ->when($type === 'entrada' || $type === 'saída', function ($query) use ($type) {
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
            ->where(function ($query) use ($enterpriseId) {
                $query->where('enterprise_id', $enterpriseId)
                    ->orWhereNull('enterprise_id');
            })
            ->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
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

    public function delete($id)
    {
        $category = $this->findById($id);
        if ($category) {
            return $category->delete();
        }

        return false;
    }
}
