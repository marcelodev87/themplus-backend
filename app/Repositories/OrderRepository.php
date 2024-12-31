<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    protected $model;

    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByUser($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
    public function getAllByCounter($counterId)
    {
        return $this->model->where('user_counter_id', $counterId)->get();
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
        $order = $this->findById($id);
        if ($order) {
            $order->update($data);

            return $order;
        }

        return null;
    }

    public function delete($id)
    {
        $order = $this->findById($id);
        if ($order) {
            return $order->delete();
        }

        return false;
    }
}
