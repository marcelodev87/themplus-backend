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
        $alert = $this->findById($id);
        if ($alert) {
            $alert->update($data);

            return $alert;
        }

        return null;
    }

    public function delete($id)
    {
        $alert = $this->findById($id);
        if ($alert) {
            return $alert->delete();
        }

        return false;
    }
}
