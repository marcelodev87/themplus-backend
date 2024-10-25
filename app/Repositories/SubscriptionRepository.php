<?php

namespace App\Repositories;

use App\Models\Subscription;

class SubscriptionRepository
{
    protected $model;

    public function __construct(Subscription $subscription)
    {
        $this->model = $subscription;
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
        $subscription = $this->model->find($id);
        if ($subscription) {
            $subscription->update($data);

            return $subscription;
        }

        return null;
    }

    public function delete($id)
    {
        $subscription = $this->model->find($id);
        if ($subscription) {
            return $subscription->delete();
        }

        return false;
    }
}
