<?php

namespace App\Services;

use App\Helpers\CellHelper;
use App\Repositories\CellMemberRepository;
use App\Repositories\CellRepository;
use App\Rules\CellRule;

class CellService
{
    protected $rule;

    protected $repository;

    protected $cellMemberRepository;

    public function __construct(
        CellRule $rule,
        CellRepository $repository,
        CellMemberRepository $cellMemberRepository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->cellMemberRepository = $cellMemberRepository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = $request->only(['name']);
        $data['date_foundation'] = $request->input('dateFoundation');
        $data['date_end'] = $request->input('dateEnd');
        $data['network_id'] = $request->input('networkID');
        $data['leader_id'] = $request->input('leaderID');
        $data['host_id'] = $request->input('hostID');
        $data['active'] = $request->input('active');
        $data['location'] = $request->input('location');
        $data['day_week'] = $request->input('dayWeek');
        $data['frequency'] = $request->input('frequency');
        $data['time'] = $request->input('time');
        $data['cep'] = $request->input('cep');
        $data['uf'] = $request->input('uf');
        $data['address'] = $request->input('address');
        $data['address_number'] = $request->input('addressNumber');
        $data['neighborhood'] = $request->input('neighborhood');
        $data['city'] = $request->input('city');
        $data['complement'] = $request->input('complement');
        $data['enterprise_id'] = $request->user()->enterprise_id;

        CellHelper::existsCell(
            $request->user()->enterprise_id,
            $request->input('name'),
            'create'
        );

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        CellHelper::existsCell(
            $request->user()->enterprise_id,
            $request->input('name'),
            'update',
            $request->input('id')
        );

        $data = $request->only(['name']);
        $data['date_foundation'] = $request->input('dateFoundation');
        $data['date_end'] = $request->input('dateEnd');
        $data['network_id'] = $request->input('networkID');
        $data['leader_id'] = $request->input('leaderID');
        $data['host_id'] = $request->input('hostID');
        $data['active'] = $request->input('active');
        $data['location'] = $request->input('location');
        $data['day_week'] = $request->input('dayWeek');
        $data['frequency'] = $request->input('frequency');
        $data['time'] = $request->input('time');
        $data['cep'] = $request->input('cep');
        $data['uf'] = $request->input('uf');
        $data['address'] = $request->input('address');
        $data['address_number'] = $request->input('addressNumber');
        $data['neighborhood'] = $request->input('neighborhood');
        $data['city'] = $request->input('city');
        $data['complement'] = $request->input('complement');

        return $this->repository->update($request->input('id'), $data);
    }
}
