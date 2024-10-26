<?php

namespace App\Repositories;

use App\Models\Alert;

class AlertRepository
{
    protected $model;

    public function __construct(Alert $alert)
    {
        $this->model = $alert;
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
        $alert = $this->model->find($id);
        if ($alert) {
            $alert->update($data);

            return $alert;
        }

        return null;
    }

    public function delete($id)
    {
        $alert = $this->model->find($id);
        if ($alert) {
            return $alert->delete();
        }

        return false;
    }
}
