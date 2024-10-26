<?php

namespace App\Repositories;

use App\Models\Department;

class DepartmentRepository
{
    protected $model;

    public function __construct(Department $department)
    {
        $this->model = $department;
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
        $department = $this->model->find($id);
        if ($department) {
            $department->update($data);
            return $department;
        }
        return null;
    }

    public function delete($id)
    {
        $department = $this->model->find($id);
        if ($department) {
            return $department->delete();
        }
        return false;
    }
}
