<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getAllByDepartment($departmentId)
    {
        return $this->model->where('department_id', $departmentId)->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function updateMember($id, array $data)
    {
        $user = $this->findById($id);
        if ($user) {
            if ($user->email !== $data['email']) {
                $existingEmail = $this->model->where('email', $data['email'])->first();
                if ($existingEmail) {
                    throw new \Exception('Email já registrado no sistema');
                }
            }
            $user->update($data);

            return $user;
        }

        return null;
    }

    public function updateData($id, array $data)
    {
        $user = $this->findById($id);
        if ($user) {
            $user->update($data);

            return $user;
        }

        return null;
    }

    public function updatePassword($id, array $data)
    {
        $user = $this->findById($id);
        if ($user) {
            $user->update($data);

            return $user;
        }

        return null;
    }

    public function updateDepartment($departmentId)
    {
        $this->model->where('department_id', $departmentId)->update(['department_id' => null]);
    }

    public function delete($id)
    {
        $user = $this->findById($id);
        if ($user) {
            return $user->delete();
        }

        return false;
    }
}
