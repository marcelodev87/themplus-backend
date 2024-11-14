<?php

namespace App\Services;

use App\Repositories\SchedulingRepository;
use App\Rules\SchedulingRule;

class SchedulingService
{
    protected $rule;

    protected $repository;

    public function __construct(
        SchedulingRule $rule,
        SchedulingRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('receipts');
        }

        $data = [
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'date_movement' => Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d'),
            'description' => $request->input('description'),
            'status' => 0,
            'receipt' => $filePath,
            'category_id' => $request->input('category'),
            'account_id' => $request->input('account'),
            'enterprise_id' => $request->user()->enterprise_id,
        ];

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        $data = [
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'description' => $request->input('description'),
            'receipt' => $request->input('file'),
            'category_id' => $request->input('category'),
            'account_id' => $request->input('account'),
            'enterprise_id' => $request->user()->enterprise_id,
        ];

        $movement = $this->repository->update($request->input('id'), $data);
    }
}
