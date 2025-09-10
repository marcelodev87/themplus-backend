<?php

namespace App\Services;

use App\Repositories\CellMemberRepository;
use App\Rules\CellMemberRule;

class CellMemberService
{
    protected $rule;

    protected $repository;

    public function __construct(
        CellMemberRule $rule,
        CellMemberRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = [];
        $data['cell_id'] = $request->input('cellID');
        $data['member_id'] = $request->input('memberID');

        return $this->repository->create($data);
    }
}
