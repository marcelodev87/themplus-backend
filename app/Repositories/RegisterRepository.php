<?php

namespace App\Repositories;

use App\Models\Register;

class RegisterRepository
{
    protected $model;

    public function __construct(Register $register)
    {
        $this->model = $register;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getAllByEnterpriseAndUser($enterpriseId, $userId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)
            ->where('user_id', $userId)
            ->get();
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
        $alert = $this->findById($id);
        if ($alert) {
            return $alert->delete();
        }

        return false;
    }
}
