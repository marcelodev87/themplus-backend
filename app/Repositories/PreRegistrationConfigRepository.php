<?php

namespace App\Repositories;

use App\Models\PreRegistrationConfig;

class PreRegistrationConfigRepository
{
    protected $model;

    public function __construct(PreRegistrationConfig $model)
    {
        $this->model = $model;
    }

    public function getByEnterprise(string $enterpriseId, array|string|null $relations = null)
    {
        $query = $this->model->where('enterprise_id', $enterpriseId);

        if ($relations) {
            $query->with($relations);
        }

        return $query->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function updateByEnterpriseId(string $enterpriseId, array $data)
    {
        return $this->model->updateOrCreate(
            ['enterprise_id' => $enterpriseId],
            $data
        );
    }
}
