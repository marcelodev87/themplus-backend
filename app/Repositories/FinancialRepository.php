<?php

namespace App\Repositories;

use App\Models\FinancialMovement;

class FinancialRepository
{
    protected $model;

    protected $movementRepository;

    public function __construct(FinancialMovement $financialMovement, MovementRepository $movementRepository)
    {
        $this->model = $financialMovement;
        $this->movementRepository = $movementRepository;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getReports($enterpriseId)
    {
        return $this->model
            ->where('enterprise_id', $enterpriseId)
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
    }

    public function countNoVerified($enterpriseId)
    {
        return $this->model
            ->where('enterprise_id', $enterpriseId)
            ->whereNull('check_counter')
            ->count();
    }

    public function mountDeliveries($enterpriseId)
    {
        return $this->movementRepository->getDeliveries($enterpriseId);
    }

    public function findById($id)
    {
        return $this->model->with('enterprise')->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $financial = $this->findById($id);
        if ($financial) {
            $financial->update($data);

            return $financial;
        }

        return null;
    }

    public function delete($id)
    {
        $financial = $this->findById($id);
        if ($financial) {
            return $financial->delete();
        }

        return false;
    }
}
