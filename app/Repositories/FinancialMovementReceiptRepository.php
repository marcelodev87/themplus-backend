<?php

namespace App\Repositories;

use App\Models\FinancialMovementReceipt;
use Illuminate\Support\Facades\Storage;

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

    public function countAllByFinancial($id)
    {
        return $this->model->where('financial_movements_id', $id)->count();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function deleteAllByFinancial($financialId)
    {
        $receipts = $this->model->where('financial_movements_id', $financialId)->get();

        foreach ($receipts as $receipt) {
            if ($receipt->receipt) {
                $oldFilePath = str_replace(env('AWS_URL').'/', '', $receipt->receipt);
                Storage::disk('s3')->delete($oldFilePath);
            }
        }

        $this->model->where('financial_movements_id', $financialId)->delete();
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
