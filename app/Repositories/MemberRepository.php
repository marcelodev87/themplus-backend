<?php

namespace App\Repositories;

use App\Models\Member;
use Illuminate\Support\Facades\DB;

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
            DB::table('networks')->where('member_id', $id)->update(['member_id' => null]);
            DB::table('ministries')->where('member_id', $id)->update(['member_id' => null]);
            DB::table('cells')->where('host_id', $id)->update(['host_id' => null]);
            DB::table('cells')->where('leader_id', $id)->update(['leader_id' => null]);
            DB::table('cell_members')->where('member_id', $id)->delete();
            DB::table('ministry_members')->where('member_id', $id)->delete();

            return $member->delete();
        }

        return false;
    }
}
