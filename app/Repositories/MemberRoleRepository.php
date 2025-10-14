<?php

namespace App\Repositories;

use App\Models\MemberRole;

class CellMemberRepository
{
    protected $model;

    public function __construct(MemberRole $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function delete($roleID, $memberID)
    {
        return $this->model
            ->where('role_id', $roleID)
            ->where('member_id', $memberID)
            ->delete();
    }
}
