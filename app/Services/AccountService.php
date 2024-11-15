<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Rules\AccountRule;
use Carbon\Carbon;

class AccountService
{
    protected $rule;

    protected $repository;

    protected $movementService;

    protected $categoryRepository;

    public function __construct(
        AccountRule $rule,
        AccountRepository $repository,
        MovementService $movementService,
        CategoryRepository $categoryRepository
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->movementService = $movementService;
        $this->categoryRepository = $categoryRepository;
    }

    public function create($request)
    {
        $this->rule->create($request);

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
            'type' => 'transferencia',
            'value' => $request->input('value'),
            'date_movement' => Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d'),
            'category_id' => $transferOut->id,
            'account_id' => $request->input('accountOut'),
            'enterprise_id' => $request->user()->enterprise_id,
        ];
        $dataEntry = [
            'type' => 'transferencia',
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

        $data = [
            'name' => $request->input('name'),
            'enterprise_id' => $request->user()->enterprise_id,
            'agency_number' => $request->input('agencyNumber'),
            'account_number' => $request->input('accountNumber'),
            'description' => $request->input('description'),
        ];

        return $this->repository->update($request->input('id'), $data);
    }
}
