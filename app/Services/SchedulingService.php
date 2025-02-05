<?php

namespace App\Services;

use App\Repositories\FinancialRepository;
use App\Repositories\SchedulingRepository;
use App\Rules\SchedulingRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SchedulingService
{
    protected $rule;

    protected $repository;

    protected $financialRepository;

    public function __construct(
        SchedulingRule $rule,
        SchedulingRepository $repository,
        FinancialRepository $financialRepository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->financialRepository = $financialRepository;
    }

    public function create($request)
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

        $programmed = (int) $request->input('programmed');
        $initialDate = Carbon::createFromFormat('d/m/Y', $request->input('date'));

        $createdSchedulings = [];

        $financial = $this->financialRepository->getReports($request->user()->enterprise_id);

        for ($i = 0; $i <= $programmed; $i++) {

            $month = $initialDate->month;
            $year = $initialDate->year;

            $exists = $financial->where('month', $month)->where('year', $year)->isNotEmpty();
            if ($exists) {
                throw new \Exception('Nos períodos de agendamentos mencionados, contém períodos em que os relatórios ja foram entregues');
            }
            $data = [
                'type' => $request->input('type'),
                'value' => $request->input('value'),
                'date_movement' => $initialDate->format('Y-m-d'),
                'description' => $request->input('description'),
                'receipt' => $fileUrl,
                'category_id' => $request->input('category'),
                'account_id' => $request->input('account'),
                'enterprise_id' => $request->user()->enterprise_id,
            ];

            $scheduling = $this->repository->create($data);
            if ($scheduling) {
                $createdSchedulings[] = $scheduling;
            }

            $initialDate->addMonthNoOverflow();
        }

        return $createdSchedulings;
    }

    public function update($request)
    {
        $this->rule->update($request);
        $scheduling = $this->repository->findById($request->input('id'));

        $data = [
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'description' => $request->input('description'),
            'date_movement' => Carbon::createFromFormat('d-m-Y', $request->input('date'))->format('Y-m-d'),
            'category_id' => $request->input('category'),
            'account_id' => $request->input('account'),
            'enterprise_id' => $request->user()->enterprise_id,
        ];

        $data = $this->handleFileUpdate($request, $scheduling, $data);

        return $this->repository->update($request->input('id'), $data);
    }

    private function handleFileUpdate($request, $scheduling, $data)
    {
        if ($request->input('file') === 'keep') {
            return $data;
        } elseif (! $request->hasFile('file')) {
            $data['receipt'] = null;
            if ($scheduling && $scheduling->receipt) {
                $oldFilePath = str_replace(env('AWS_URL').'/', '', $scheduling->receipt);
                if ($oldFilePath) {
                    Storage::disk('s3')->delete($oldFilePath);
                }
            }

            return $data;
        } else {
            if ($scheduling && $scheduling->receipt) {
                $oldFilePath = str_replace(env('AWS_URL').'/', '', $scheduling->receipt);
                if ($oldFilePath) {
                    Storage::disk('s3')->delete($oldFilePath);
                }
            }
            $env = env('APP_ENV');
            $folder = match ($env) {
                'local' => 'receipts-local',
                'development' => 'receipts-development',
                'production' => 'receipts-production',
                default => 'receipts',
            };

            $path = $request->file('file')->store($folder, 's3');
            $data['receipt'] = Storage::disk('s3')->url($path);

            return $data;
        }
    }
}
