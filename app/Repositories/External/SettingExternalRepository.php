<?php

namespace App\Repositories\External;

use App\Models\External\SettingExternal;

class SettingExternalRepository
{
    protected $model;

    public function __construct(SettingExternal $model)
    {
        $this->model = $model;
    }

    public function getAllSettings()
    {
        $response = $this->model->all();

        return $response;
    }

    public function getSettingKey($key)
    {
        return $this->model->where('key', $key)->first();
    }
}
