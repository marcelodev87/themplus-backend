<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleRepository
{
    protected $model;

    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId, array $relations = null)
    {
        $query = $this->model->where('enterprise_id', $enterpriseId);

        if (!empty($relations)) {
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
        $role = $this->findById($id);
        if ($role) {
            $role->update($data);

            return $role;
        }

        return null;
    }

    public function delete($id)
    {
        $role = $this->findById($id);
        if ($role) {
            DB::table('member_role')->where('role_id', $id)->delete();

            return $role->delete();
        }

        return false;
    }
}
