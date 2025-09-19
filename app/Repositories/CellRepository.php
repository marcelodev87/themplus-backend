<?php

namespace App\Repositories;

use App\Models\Cell;

class CellRepository
{
    protected $model;

    public function __construct(Cell $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise(string $enterpriseId, array|string|null $relations = null)
    {
        $query = $this->model->where('enterprise_id', $enterpriseId);

        if ($relations) {
            $query->with($relations);
        }

        return $query->get();
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
        $network = $this->findById($id);
        if ($network) {
            $network->update($data);

            return $network;
        }

        return null;
    }

    public function delete($id)
    {
        $network = $this->findById($id);
        if ($network) {
            return $network->delete();
        }

        return false;
    }
}
