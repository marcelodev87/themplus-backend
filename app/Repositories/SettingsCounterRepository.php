<?php

namespace App\Repositories;

use App\Models\SettingsCounter;

class SettingsCounterRepository
{
    protected $model;

    public function __construct(SettingsCounter $settingsCounter)
    {
        $this->model = $settingsCounter;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->first();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($enterpriseId, array $data)
    {
        $setting = $this->model->where('enterprise_id', $enterpriseId)->first();
        if ($setting) {
            $setting->update($data);

            return $setting;
        }

        return null;
    }

    public function delete($id)
    {
        $setting = $this->findById($id);
        if ($setting) {
            return $setting->delete();
        }

        return false;
    }
}
