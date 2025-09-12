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
        $data['congregation_id'] = $request->input('congregationID');
        $data['start_date'] = $request->input('startDate');
        // $data['reason_start_date'] = $request->input('reasonStartDate');
        $data['church_start_date'] = $request->input('churchStartDate');
        $data['end_date'] = $request->input('endDate');
        // $data['reason_end_date'] = $request->input('reasonEndDate');
        $data['church_end_date'] = $request->input('churchEndDate');
        $data['role_id'] = $request->input('roleID');
        $data['enterprise_id'] = $request->user()->enterprise_id;

        return $this->repository->create($data);
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
        $data['congregation_id'] = $request->input('congregationID');
        $data['start_date'] = $request->input('startDate');
        // $data['reason_start_date'] = $request->input('reasonStartDate');
        $data['church_start_date'] = $request->input('churchStartDate');
        $data['end_date'] = $request->input('endDate');
        // $data['reason_end_date'] = $request->input('reasonEndDate');
        $data['church_end_date'] = $request->input('churchEndDate');
        $data['role_id'] = $request->input('roleID');

        return $this->repository->update($request->input('id'), $data);

    }
}
