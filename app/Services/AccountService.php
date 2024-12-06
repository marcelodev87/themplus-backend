<?php

namespace App\Services;

use App\Helpers\AccountHelper;
use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\MovementRepository;
use App\Repositories\SchedulingRepository;
use App\Rules\AccountRule;
use Carbon\Carbon;

class AccountService
{
    protected $rule;

    protected $repository;

    protected $movementService;

    protected $movementRepository;

    protected $schedulingRepository;

    protected $categoryRepository;

    public function __construct(
        AccountRule $rule,
        AccountRepository $repository,
        MovementService $movementService,
        CategoryRepository $categoryRepository,
        MovementRepository $movementRepository,
        SchedulingRepository $schedulingRepository
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->movementService = $movementService;
        $this->categoryRepository = $categoryRepository;
        $this->schedulingRepository = $schedulingRepository;
        $this->movementRepository = $movementRepository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        AccountHelper::existsAccount(
            $request->input('name'),
            $request->user()->enterprise_id,
            $request->input('agencyNumber'),
            $request->input('accountNumber'),
            'create',
            null
        );

        $data = [
            'name' => $request->input('name'),
            'balance' => 0,
            'enterprise_id' => $request->user()->enterprise_id,
            'agency_number' => $request->input('agencyNumber'),
            'account_number' => $request->input('accountNumber'),
            'description' => $request->input('description'),
        ];

        return $this->repository->create($data);
    }

    public function createTransfer($request)
    {
        $this->rule->createTransfer($request);

        $transferEntry = $this->categoryRepository->findByName('Transferência', 'entrada');
        $transferOut = $this->categoryRepository->findByName('Transferência', 'saída');

        $dataOut = [
            'type' => 'saída',
            'value' => $request->input('value'),
            'date_movement' => Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d'),
            'category_id' => $transferOut->id,
            'account_id' => $request->input('accountOut'),
            'enterprise_id' => $request->user()->enterprise_id,
        ];
        $dataEntry = [
            'type' => 'entrada',
            'value' => $request->input('value'),
            'date_movement' => Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d'),
            'category_id' => $transferEntry->id,
            'account_id' => $request->input('accountEntry'),
            'enterprise_id' => $request->user()->enterprise_id,
        ];

        return $this->movementService->createTransfer($dataOut, $dataEntry);
    }

    public function update($request)
    {
        $this->rule->update($request);

        AccountHelper::existsAccount(
            $request->input('name'),
            $request->user()->enterprise_id,
            $request->input('agencyNumber'),
            $request->input('accountNumber'),
            'update',
            $request->input('id')
        );

        $data = [
            'name' => $request->input('name'),
            'enterprise_id' => $request->user()->enterprise_id,
            'agency_number' => $request->input('agencyNumber'),
            'account_number' => $request->input('accountNumber'),
            'description' => $request->input('description'),
        ];

        return $this->repository->update($request->input('id'), $data);
    }

    public function updateActive($id)
    {
        $data['active'] = 1;

        return $this->repository->update($id, $data);

    }

    public function delete($id)
    {
        $this->rule->delete($id);

        $movements = $this->movementRepository->getAllByAccount($id);
        $schedulings = $this->schedulingRepository->getAllByAccount($id);

        if ($movements->isNotEmpty() || $schedulings->isNotEmpty()) {
            $data['active'] = 0;
            $result = $this->repository->update($id, $data);

            return [
                'message' => 'Conta inativada, pois possui movimentações ou agendamentos vinculados',
                'data' => $result,
                'inactivated' => true,
            ];
        } else {
            $result = $this->repository->delete($id);

            return [
                'message' => 'Conta deletada com sucesso',
                'data' => $result,
                'inactivated' => false,
            ];
        }
    }
}
