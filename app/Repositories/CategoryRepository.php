<?php

namespace App\Repositories;

use App\Models\Category;

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

    public function getAllByEnterpriseWithDefaults($enterpriseId, $type = null)
    {
        return $this->model->where(function ($query) use ($enterpriseId) {
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
