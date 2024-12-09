<?php

namespace App\Repositories;

use App\Models\Scheduling;
use App\Services\MovementService;
use Carbon\Carbon;

class SchedulingRepository
{
    protected $model;

    protected $movementService;

    public function __construct(Scheduling $scheduling, MovementService $movementService)
    {
        $this->model = $scheduling;
        $this->movementService = $movementService;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getAllByCategory($categoryId)
    {
        return $this->model->where('category_id', $categoryId)->get();
    }

    public function getAllByAccount($accountId)
    {
        return $this->model->where('account_id', $accountId)->get();
    }

    public function getSchedulingsDashboard($enterpriseId, $date)
    {
        $carbonDate = Carbon::createFromFormat('m-Y', $date);

        $month = $carbonDate->month;
        $year = $carbonDate->year;

        $dateColumn = 'created_at';

        $schedulings = $this->model
            ->where('schedulings.enterprise_id', $enterpriseId)
            ->whereYear("schedulings.$dateColumn", $year)
            ->whereMonth("schedulings.$dateColumn", $month)
            ->join('categories', 'schedulings.category_id', '=', 'categories.id')
            ->selectRaw('
                SUM(CASE WHEN categories.type = "entrada" THEN schedulings.value ELSE 0 END) as entry_value,
                SUM(CASE WHEN categories.type = "saida" THEN schedulings.value ELSE 0 END) as out_value
            ')
            ->first();

        $entryValue = $schedulings->entry_value ?? 0;
        $outValue = $schedulings->out_value ?? 0;

        return [
            'entry_value' => number_format($entryValue, 2, '.', ''),
            'out_value' => number_format($outValue, 2, '.', ''),
            'month_year' => $date,
        ];
    }

    public function getAllByEnterpriseWithRelations($enterpriseId)
    {
        return $this->model->with(['account', 'category'])
            ->where('enterprise_id', $enterpriseId)
            ->get();
    }

    public function getAllByEnterpriseWithRelationsWithParams($request)
    {
        $expired = $request->has('expired') ? filter_var($request->query('expired'), FILTER_VALIDATE_BOOLEAN) : null;
        $entry = $request->has('entry') ? filter_var($request->query('entry'), FILTER_VALIDATE_BOOLEAN) : null;
        $out = $request->has('out') ? filter_var($request->query('out'), FILTER_VALIDATE_BOOLEAN) : null;

        $query = $this->model->with(['account', 'category'])
            ->where('enterprise_id', $request->user()->enterprise_id);

        if ($out !== null && $out) {
            $query->where('type', 'saída');
        }

        if ($entry !== null && $entry) {
            $query->where('type', 'entrada');
        }

        if ($expired !== null && $expired) {
            $yesterday = Carbon::yesterday()->format('Y-m-d');
            $query->where('date_movement', '<', $yesterday);
        }

        return $query->get();
    }

    public function getAllByEnterpriseWithRelationsWithParamsByDate($request, $date)
    {
        $expired = $request->has('expired') ? filter_var($request->query('expired'), FILTER_VALIDATE_BOOLEAN) : null;
        $entry = $request->has('entry') ? filter_var($request->query('entry'), FILTER_VALIDATE_BOOLEAN) : null;
        $out = $request->has('out') ? filter_var($request->query('out'), FILTER_VALIDATE_BOOLEAN) : null;

        $query = $this->model->with(['account', 'category'])
            ->where('enterprise_id', $request->user()->enterprise_id);

        if ($out !== null && $out) {
            $query->where('type', 'saída');
        }

        if ($entry !== null && $entry) {
            $query->where('type', 'entrada');
        }

        if ($expired !== null && $expired) {
            $yesterday = Carbon::yesterday()->format('Y-m-d');
            $query->where('date_movement', '<', $yesterday);
        }

        if ($date) {
            [$month, $year] = explode('-', $date);

            if (! is_numeric($month) || ! is_numeric($year) || strlen($month) !== 2 || strlen($year) !== 4) {
                return collect();
            }

            $query->whereMonth('date_movement', $month)
                ->whereYear('date_movement', $year);
        }

        return $query->get();
    }

    public function getAllByEnterpriseWithRelationsByDate($enterpriseId, $date)
    {
        $query = $this->model->with(['account', 'category'])
            ->where('enterprise_id', $enterpriseId);

        [$month, $year] = explode('-', $date);

        if (! is_numeric($month) || ! is_numeric($year) || strlen($month) !== 2 || strlen($year) !== 4) {
            return collect();
        }

        $query->whereMonth('date_movement', $month)
            ->whereYear('date_movement', $year);

        return $query->get();
    }

    public function getMonthYears($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)
            ->selectRaw('DATE_FORMAT(date_movement, "%m/%Y") as month_year')
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->pluck('month_year');
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByIdWithRelations($id)
    {
        return $this->model->with(['account', 'category'])->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $scheduling = $this->findById($id);
        if ($scheduling) {
            return $scheduling->update($data);
        }

        return null;
    }

    public function export($out, $entry, $expired, $date, $enterpriseId)
    {
        $query = $this->model->with(['account', 'category'])
            ->where('enterprise_id', $enterpriseId);

        if ($out) {
            $query->where('type', 'saída');
        }

        if ($entry) {
            $query->where('type', 'entrada');
        }

        if ($expired) {
            $query->whereDate('date_movement', '<', now()->toDateString());
        }

        if ($date) {
            [$month, $year] = explode('-', $date);

            if (is_numeric($month) && is_numeric($year) && strlen($month) === 2 && strlen($year) === 4) {
                $query->whereMonth('date_movement', $month)
                    ->whereYear('date_movement', $year);
            } else {
                throw new \InvalidArgumentException('Formato de data inválida, use MM/YYYY.');
            }
        }

        return $query->get();
    }

    public function finalize($id)
    {
        $scheduling = $this->findById($id);
        if ($scheduling) {
            $result = $this->movementService->includeScheduling($scheduling->toArray());
            if ($result) {
                return $scheduling->delete();
            }

        }

        return null;
    }

    public function delete($id)
    {
        $scheduling = $this->findById($id);
        if ($scheduling) {
            return $scheduling->delete();
        }

        return false;
    }
}
