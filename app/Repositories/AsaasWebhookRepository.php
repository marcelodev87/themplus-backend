<?php

namespace App\Repositories;

use App\Models\AsaasWebhook;

class AsaasWebhookRepository
{
    protected $model;

    public function __construct(AsaasWebhook $webhook)
    {
        $this->model = $webhook;
    }

    public function getAll()
    {
        return $this->model->all();
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

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $webhook = $this->findByPaymentID($id);
        if ($webhook) {
            $webhook->update($data);

            return $webhook;
        }

        return null;
    }
}
