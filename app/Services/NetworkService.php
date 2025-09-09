<?php

namespace App\Services;

use App\Helpers\NetworkHelper;
use App\Repositories\NetworkRepository;
use App\Rules\NetworkRule;

class NetworkService
{
    protected $rule;

    protected $repository;

    public function __construct(
        NetworkRule $rule,
        NetworkRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = $request->only(['name', 'member_id', 'congregation_id']);
        $data['enterprise_id'] = $request->user()->enterprise_id;

        NetworkHelper::existsNetwork(
            $request->user()->enterprise_id,
            $request->input('name'),
            'create',
        );

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        $data = $request->only(['name', 'member_id', 'congregation_id']);
        $data['enterprise_id'] = $request->user()->enterprise_id;

        NetworkHelper::existsNetwork(
            $request->user()->enterprise_id,
            $request->input('name'),
            'update',
            $request->input('id')
        );

        return $this->repository->update($request->input('id'), $data);
    }
}
