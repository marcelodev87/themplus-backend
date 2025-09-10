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
        $data['congregation_id'] = $request->input('congregationID');
        $data['host_id'] = $request->input('hostID');
        $data['active'] = $request->input('active');
        $data['location'] = $request->input('location');
        $data['day_week'] = $request->input('dayWeek');
        $data['frequency'] = $request->input('frequency');
        $data['time'] = $request->input('time');
        $data['location_address_member'] = $request->input('locationAddressMember');
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

        $cell = $this->repository->create($data);

        if ($request->has('members') && is_array($request->members)) {
            foreach ($request->members as $member) {
                $this->cellMemberRepository->create([
                    'member_id' => $member['id'],
                    'cell_id' => $cell->id,
                ]);
            }
        }

        return $cell;
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
        $data['congregation_id'] = $request->input('congregationID');
        $data['host_id'] = $request->input('hostID');
        $data['active'] = $request->input('active');
        $data['location'] = $request->input('location');
        $data['day_week'] = $request->input('dayWeek');
        $data['frequency'] = $request->input('frequency');
        $data['time'] = $request->input('time');
        $data['location_address_member'] = $request->input('locationAddressMember');
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
