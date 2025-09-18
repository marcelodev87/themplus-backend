<?php

namespace App\Services;

use App\Helpers\CongregationHelper;
use App\Repositories\CongregationRepository;
use App\Rules\CongregationRule;

class CongregationService
{
    protected $rule;

    protected $repository;

    public function __construct(
        CongregationRule $rule,
        CongregationRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        CongregationHelper::existsCongregation(
            $request->user()->enterprise_id,
            $request->input('name'),
            'create',
        );

        $data = $request->only([
            'name',
            'cnpj',
            'cpf',
            'email',
            'phone',
            'cep',
            'uf',
            'address',
            'neighborhood',
            'city',
            'complement',
        ]);
        $data['date_foundation'] = $request->input('dateFoundation');
        $data['address_number'] = $request->input('addressNumber');
        $data['member_id'] = $request->input('memberID');
        $data['enterprise_id'] = $request->user()->enterprise_id;

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        CongregationHelper::existsCongregation(
            $request->user()->enterprise_id,
            $request->input('name'),
            'update',
            $request->input('id')
        );

        $data = $request->only([
            'name',
            'cnpj',
            'cpf',
            'email',
            'phone',
            'cep',
            'uf',
            'address',
            'neighborhood',
            'city',
            'complement',
        ]);
        $data['date_foundation'] = $request->input('dateFoundation');
        $data['address_number'] = $request->input('addressNumber');
        $data['member_id'] = $request->input('memberID');

        return $this->repository->update($request->input('id'), $data);

    }
}
