<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

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
    public function getUsersByPhone($phone)
    {
        return $this->model->where('phone', $phone)->get();
    }

    public function getAllByEnterpriseWithRelations($enterpriseId)
    {
        return $this->model->with(['department'])
            ->where('enterprise_id', $enterpriseId)
            ->get();
    }

    public function getAllByDepartment($departmentId)
    {
        return $this->model->where('department_id', $departmentId)->get();
    }

    public function getUsersDashboard($enterpriseId)
    {
        $amountUsers = $this->model->where('enterprise_id', $enterpriseId)->count();

        $amountCommonUsers = $this->model
            ->where('enterprise_id', $enterpriseId)
            ->where('position', 'common_user')
            ->count();

        $amountAdmins = $this->model
            ->where('enterprise_id', $enterpriseId)
            ->where('position', 'admin')
            ->count();

        return [
            'amount_users' => $amountUsers,
            'amount_common_users' => $amountCommonUsers,
            'amount_admins' => $amountAdmins,
        ];
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function update($id, array $data)
    {
        $user = $this->findById($id);
        if ($user) {
            $user->update($data);

            return $user;
        }

        return null;
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

    public function resetPassword($email, array $data)
    {
        $user = $this->findByEmail($email);
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
            DB::table('notifications')->where('user_id', $id)->delete();
            DB::table('registers')->where('user_id', $id)->delete();
            DB::table('feedbacks')->where('user_id', $id)->delete();

            return $user->delete();
        }

        return false;
    }
}
