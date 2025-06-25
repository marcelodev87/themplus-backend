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
        return $this->model->all();
    }

    public function getSettingKey($key)
    {
        return $this->model->where('key', $key)->first();
    }
}
