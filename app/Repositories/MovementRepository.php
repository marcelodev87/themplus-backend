<?php

namespace App\Repositories;

use App\Models\Movement;

class MovementRepository
{
    protected $model;

    public function __construct(Movement $movement)
    {
        $this->model = $movement;
    }

    public function getAll()
    {
        return $this->model->all();
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
        $movement = $this->model->find($id);
        if ($movement) {
            $movement->update($data);
            return $movement;
        }
        return null;
    }

    public function delete($id)
    {
        $movement = $this->model->find($id);
        if ($movement) {
            return $movement->delete();
        }
        return false;
    }
}
