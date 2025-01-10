<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    protected $model;

    protected $enterpriseRepository;

    public function __construct(Order $order, EnterpriseRepository $enterpriseRepository)
    {
        $this->model = $order;
        $this->enterpriseRepository = $enterpriseRepository;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByUser($enterpriseId)
    {
        return $this->model->with(['counter:id,name,email,cpf,cnpj'])
            ->where('enterprise_id', $enterpriseId)
            ->get();
    }

    public function checkExistOrders($enterpriseId, $enterpriseCounterId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)
            ->where('enterprise_counter_id', $enterpriseCounterId)
            ->get();
    }

    public function getAllByCounter($enterpriseCounterId)
    {
        return $this->model->with(['enterprise:id,name,email,cpf,cnpj'])
            ->where('enterprise_counter_id', $enterpriseCounterId)
            ->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByIdWithCounter($id)
    {
        return $this->model->with(['counter:id,name,email,cpf,cnpj'])->find($id);
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

    public function deleteBond($id)
    {
        return $this->enterpriseRepository->deleteBond($id);
    }
}
