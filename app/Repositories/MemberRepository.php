<?php

namespace App\Repositories;

use App\Models\Enterprise;
use App\Models\Member;
use App\Models\MemberRelationship;
use Illuminate\Support\Facades\DB;

class MemberRepository
{
    protected $model;

    protected $modelMemberRelationship;

    public function __construct(Member $model, MemberRelationship $modelMemberRelationship)
    {
        $this->model = $model;
        $this->modelMemberRelationship = $modelMemberRelationship;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise(
        string $enterpriseId,
        ?array $relations = null,
        ?int $active = null
    ) {
        $enterpriseIds = Enterprise::where('id', $enterpriseId)
            ->orWhere('created_by', $enterpriseId)
            ->pluck('id');

        $query = $this->model->whereIn('enterprise_id', $enterpriseIds);

        if (! empty($relations)) {
            $query->with($relations);
        }

        if ($active !== null) {
            $query->where('active', $active);
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
            DB::table('movements')->where('member_id', $id)->update(['member_id' => null]);
            DB::table('schedulings')->where('member_id', $id)->update(['member_id' => null]);
            DB::table('cells')->where('host_id', $id)->update(['host_id' => null]);
            DB::table('cells')->where('leader_id', $id)->update(['leader_id' => null]);
            DB::table('cell_members')->where('member_id', $id)->delete();
            DB::table('ministry_members')->where('member_id', $id)->delete();
            DB::table('member_relationship')->where('related_member_id', $id)->delete();

            return $member->delete();
        }

        return false;
    }

    public function deleteRelationship($member_id, $related_member_id, $relationship_id)
    {
        return $this->modelMemberRelationship
                ->where('member_id', $member_id)
                ->where('related_member_id', $related_member_id)
                ->where('relationship_id', $relationship_id)
                ->delete();
    }
}
