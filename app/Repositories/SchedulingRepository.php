<?php

namespace App\Repositories;

use App\Models\Scheduling;

class SchedulingRepository
{
    protected $model;

    public function __construct(Scheduling $scheduling)
    {
        $this->model = $scheduling;
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
        $scheduling = $this->findById($id);
        if ($scheduling) {
            $scheduling->update($data);

            return $scheduling;
        }

        return null;
    }

    public function delete($id)
    {
        $scheduling = $this->findById($id);
        if ($scheduling) {
            return $scheduling->delete();
        }

        return false;
    }
}
