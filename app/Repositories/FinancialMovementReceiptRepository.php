<?php

namespace App\Repositories;

use App\Models\FinancialMovementReceipt;

class FinancialMovementReceiptRepository
{
    protected $model;

    public function __construct(FinancialMovementReceipt $model)
    {
        $this->model = $model;
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getAllByFinancial($id)
    {
        return $this->model->where('financial_movements_id', $id)->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function delete($id)
    {
        $receipt = $this->findById($id);
        if ($receipt) {
            return $receipt->delete();
        }

        return false;
    }
}
