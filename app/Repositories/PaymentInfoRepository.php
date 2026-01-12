<?php

namespace App\Repositories;

use App\Models\PaymentInfo;

class PaymentInfoRepository
{
    protected $model;

    public function __construct(PaymentInfo $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data, array $relations = []): ?PaymentInfo
    {
        if ($this->model instanceof PaymentInfo) {
            $record = $this->findByUserId($id, $relations);
        } else {
            $record = $this->findById($id, $relations);
        }

        if (! $record) {
            return null;
        }

        $record->update($data);

        return $record;
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByPaymentID($id)
    {
        return $this->model->where('payment_id', $id)->first();
    }

    public function findByUserId($id)
    {
        return $this->model->where('user_id', $id)->first();
    }

    public function deleteByPaymentId($id)
    {
        $paymentInfo = $this->findByPaymentID($id);

        if (! $paymentInfo) {
            return null;
        }

        $paymentInfo->delete();

        return $paymentInfo;
    }

    public function deleteByUserId($id)
    {
        $paymentInfo = $this->findByUserId($id);
        if ($paymentInfo) {

            return $paymentInfo->delete();
        }

        return true;
    }
}
