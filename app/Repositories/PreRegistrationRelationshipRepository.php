<?php

namespace App\Repositories;

use App\Models\PreRegistrationRelationship;

class PreRegistrationRelationshipRepository
{
    protected $model;

    public function __construct(PreRegistrationRelationship $model)
    {
        $this->model = $model;
    }

    public function getAllByEnterprise(string $enterpriseId, array|string|null $relations = null)
    {
        $query = $this->model->where('enterprise_id', $enterpriseId);

        if ($relations) {
            $query->with($relations);
        }

        return $query->get();
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
        $registration = $this->findById($id);
        if ($registration) {
            $registration->update($data);

            return $registration;
        }

        return null;
    }
}
