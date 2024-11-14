<?php

namespace App\Repositories;

use App\Models\Scheduling;
use App\Services\MovementService;

class SchedulingRepository
{
    protected $model;

    protected $movementService;

    public function __construct(Scheduling $scheduling, MovementService $movementService)
    {
        $this->model = $scheduling;
        $this->movementService = $movementService;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getAllByEnterpriseWithRelations($enterpriseId)
    {
        return $this->model->with(['account', 'category'])
            ->where('enterprise_id', $enterpriseId)
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
        $scheduling = $this->findById($id);
        if ($scheduling) {
            $scheduling->update($data);

            return $scheduling;
        }

        return null;
    }

    public function finalize($id)
    {
        $scheduling = $this->findById($id);
        if ($scheduling) {
            $result = $this->movementService->includeScheduling($scheduling->toArray());
            if ($result) {
                return $scheduling->delete();
            }

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
