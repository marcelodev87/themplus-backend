<?php

namespace App\Repositories;

use App\Models\Relationship;

class RelationshipRepository
{
    protected $model;

    public function __construct(Relationship $relationship)
    {
        $this->model = $relationship;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model
            ->where('enterprise_id', $enterpriseId)
            ->orderBy('name')
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

    public function update($id, array $data)
    {
        $relationship = $this->findById($id);
        if ($relationship) {
            $relationship->update($data);

            return $relationship;
        }

        return null;
    }

    public function delete($id)
    {
        $relationship = $this->findById($id);
        if ($relationship) {
            return $relationship->delete();
        }

        return false;
    }
}
