<?php

namespace App\Repositories;

use App\Models\FinancialMovement;

class FinancialMovementRepository
{
    protected $model;

    public function __construct(FinancialMovement $financialMovement)
    {
        $this->model = $financialMovement;
    }

    public function getAll()
    {
        return $this->model->all();
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
        $financialMovement = $this->model->find($id);
        if ($financialMovement) {
            $financialMovement->update($data);
            return $financialMovement;
        }
        return null;
    }

    public function delete($id)
    {
        $financialMovement = $this->model->find($id);
        if ($financialMovement) {
            return $financialMovement->delete();
        }
        return false;
    }
}
