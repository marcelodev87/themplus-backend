<?php

namespace App\Services;

use App\Helpers\MemberHelper;
use App\Repositories\MemberRepository;
use App\Rules\MemberRule;

class MemberService
{
    protected $rule;

    protected $repository;

    public function __construct(
        MemberRule $rule,
        MemberRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        MemberHelper::existsMember(
            $request->user()->enterprise_id,
            $request->input('name'),
            'create',
        );

        MemberHelper::existsMemberWithCpf(
            $request->user()->enterprise_id,
            $request->input('cpf'),
            'create',
        );

        $data = $request->only([
            'name',
            'profession',
            'naturalness',
            'education',
            'cpf',
            'email',
            'phone',
            'cep',
            'uf',
            'address',
            'neighborhood',
            'city',
            'complement',
            'type',
            'active',
        ]);
        $data['date_birth'] = $request->input('dateBirth');
        $data['marital_status'] = $request->input('maritalStatus');
        $data['email_professional'] = $request->input('emailProfessional');
        $data['phone_professional'] = $request->input('phoneProfessional');
        $data['address_number'] = $request->input('addressNumber');
        $data['date_baptismo'] = $request->input('dateBaptismo');
        $data['start_date'] = $request->input('startDate');
        // $data['reason_start_date'] = $request->input('reasonStartDate');
        $data['church_start_date'] = $request->input('churchStartDate');
        $data['end_date'] = $request->input('endDate');
        // $data['reason_end_date'] = $request->input('reasonEndDate');
        $data['church_end_date'] = $request->input('churchEndDate');
        $data['role_id'] = $request->input('roleID');
        $data['enterprise_id'] = $request->user()->enterprise_id;

        $member = $this->repository->create($data);
        if ($request->has('roles') && is_array($request->input('roles'))) {
            $member->roles()->sync($request->input('roles'));
        }
        if ($request->has('ministries') && is_array($request->input('ministries'))) {
            $member->ministries()->sync($request->input('ministries'));
        }
        if ($request->has('family') && is_array($request->input('family'))) {
            $familyData = $request->input('family');
            $syncData = [];

            foreach ($familyData as $relation) {
                $syncData[$relation['memberID']] = [
                    'relationship_id' => $relation['relationshipID'],
                ];
            }

            $member->family()->sync($syncData);
        }

        return $member;
    }

    public function update($request)
    {
        $this->rule->update($request);

        MemberHelper::existsMember(
            $request->user()->enterprise_id,
            $request->input('name'),
            'update',
            $request->input('id')
        );

        MemberHelper::existsMemberWithCpf(
            $request->user()->enterprise_id,
            $request->input('cpf'),
            'create',
            $request->input('id')
        );

        $data = $request->only([
            'name',
            'profession',
            'naturalness',
            'education',
            'cpf',
            'email',
            'phone',
            'cep',
            'uf',
            'address',
            'neighborhood',
            'city',
            'complement',
            'type',
            'active',
        ]);
        $data['date_birth'] = $request->input('dateBirth');
        $data['marital_status'] = $request->input('maritalStatus');
        $data['email_professional'] = $request->input('emailProfessional');
        $data['phone_professional'] = $request->input('phoneProfessional');
        $data['address_number'] = $request->input('addressNumber');
        $data['date_baptismo'] = $request->input('dateBaptismo');
        $data['start_date'] = $request->input('startDate');
        // $data['reason_start_date'] = $request->input('reasonStartDate');
        $data['church_start_date'] = $request->input('churchStartDate');
        $data['end_date'] = $request->input('endDate');
        // $data['reason_end_date'] = $request->input('reasonEndDate');
        $data['church_end_date'] = $request->input('churchEndDate');

        $member = $this->repository->update($request->input('id'), $data);

        if ($request->has('roles')) {
            $member->roles()->sync($request->input('roles'));
        }
        if ($request->has('ministries')) {
            $member->ministries()->sync($request->input('ministries'));
        }
        if ($request->has('family') && is_array($request->input('family'))) {
            $familyData = $request->input('family');
            $syncData = [];

            foreach ($familyData as $relation) {
                $syncData[$relation['memberID']] = [
                    'relationship_id' => $relation['relationshipID'],
                ];
            }

            $member->family()->sync($syncData);
        }

        return $member;

    }
}
