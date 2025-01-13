<?php

namespace App\Repositories;

use App\Models\Enterprise;

class EnterpriseRepository
{
    protected $model;

    public function __construct(Enterprise $enterprise)
    {
        $this->model = $enterprise;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllOfficesByEnterprise($enterpriseId)
    {
        return $this->model->with('users')
            ->where('created_by', $enterpriseId)
            ->get();
    }

    public function getAllViewEnterprises($enterpriseId)
    {
        return $this->model->select('id', 'name')
            ->where('created_by', $enterpriseId)
            ->get();
    }

    public function getBonds($enterpriseId)
    {
        return $this->model->select('id', 'name', 'cnpj', 'cpf', 'email', 'phone', 'created_by')
            ->where('counter_enterprise_id', $enterpriseId)->get();
    }

    public function searchEnterprise($enterpriseId, $text)
    {
        return $this->model->where('id', '!=', $enterpriseId)
            ->whereNull('counter_enterprise_id')
            ->whereNull('created_by')
            ->where('position', 'client')
            ->where(function ($query) use ($text) {
                $query->where('cpf', 'like', "%{$text}%")
                    ->orWhere('cnpj', 'like', "%{$text}%")
                    ->orWhere('name', 'like', "%{$text}%")
                    ->orWhere('email', 'like', "%{$text}%");
            })
            ->select('id', 'name', 'email', 'cpf', 'cnpj')
            ->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByCpf($cpf)
    {
        return $this->model->where('cpf', $cpf)->first();
    }

    public function findByCnpj($cnpj)
    {
        return $this->model->where('cnpj', $cnpj)->first();
    }

    public function createStart($data)
    {
        return $this->model->create($data);
    }

    public function createOffice(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $enterprise = $this->findById($id);
        if ($enterprise) {
            $enterprise->update($data);

            return $enterprise;
        }

        return null;
    }

    public function deleteBond($id)
    {
        $enterprise = $this->findById($id);
        if ($enterprise) {
            $enterprise->update(['counter_enterprise_id' => null]);

            $offices = $this->getAllOfficesByEnterprise($id);
            foreach ($offices as $office) {
                $office->update(['counter_enterprise_id' => null]);
            }

            return $enterprise;
        }

        return null;
    }

    public function delete($id)
    {
        $enterprise = $this->findById($id);
        if ($enterprise) {
            return $enterprise->delete();
        }

        return false;
    }
}
