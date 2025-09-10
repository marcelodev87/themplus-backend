<?php

namespace App\Repositories;

use App\Models\Member;

class MemberRepository
{
    protected $model;

    public function __construct(Member $model)
    {
        $this->model = $model;
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
        $member = $this->findById($id);
        if ($member) {
            $member->update($data);

            return $member;
        }

        return null;
    }

    public function delete($id)
    {
        $member = $this->findById($id);
        if ($member) {
            return $member->delete();
        }

        return false;
    }
}
