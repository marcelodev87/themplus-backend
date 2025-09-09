<?php

namespace App\Repositories;

use App\Models\Congregation;

class CongregationRepository
{
    protected $model;

    public function __construct(Congregation $congregation)
    {
        $this->model = $congregation;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
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
        $congregation = $this->findById($id);
        if ($congregation) {
            $congregation->update($data);

            return $congregation;
        }

        return null;
    }

    public function delete($id)
    {
        $congregation = $this->findById($id);
        if ($congregation) {
            return $congregation->delete();
        }

        return false;
    }
}
