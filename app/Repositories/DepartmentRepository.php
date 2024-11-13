<?php

namespace App\Repositories;

use App\Models\Department;

class DepartmentRepository
{
    protected $model;

    protected $userRepository;

    public function __construct(Department $department, UserRepository $userRepository)
    {
        $this->model = $department;
        $this->userRepository = $userRepository;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
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
        $department = $this->findById($id);
        if ($department) {
            $department->update($data);

            return $department;
        }

        return null;
    }

    private function deleteChildren($id)
    {
        $children = $this->model->where('parent_id', $id)->get();

        foreach ($children as $child) {
            $this->deleteChildren($child->id);
            $child->delete();
        }
    }

    private function updateDepartmentUser($departmentId)
    {
        $this->userRepository->update($departmentId);
    }

    public function delete($id): void
    {
        $department = $this->findById($id);

        if ($department) {
            $this->deleteChildren($department->id);
            $this->updateDepartmentUser($department->id);
            $department->delete();
        }

    }
}
