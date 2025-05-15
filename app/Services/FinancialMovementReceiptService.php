<?php

namespace App\Services;

use App\Repositories\FinancialMovementReceiptRepository;
use App\Rules\FinancialMovementReceiptRule;
use Illuminate\Support\Facades\Storage;

class FinancialMovementReceiptService
{
    protected $rule;

    protected $repository;

    public function __construct(
        FinancialMovementReceiptRule $rule,
        FinancialMovementReceiptRepository $repository

    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request, $financialId)
    {
        $this->rule->create($request);

        $fileUrl = null;

        if ($request->hasFile('file')) {
            $env = env('APP_ENV');

            $folder = match ($env) {
                'local' => 'receipts-local',
                'development' => 'receipts-development',
                'production' => 'receipts-production',
                default => 'receipts',
            };

            $path = $request->file('file')->store($folder, 's3');

            $fileUrl = Storage::disk('s3')->url($path);
        }

        $data = [
            'name' => $request->input('name'),
            'receipt' => $fileUrl,
            'enterprise_id' => $request->user()->enterprise_id,
            'financial_movements_id' => $financialId,
        ];

        return $this->repository->create($data);
    }
}
