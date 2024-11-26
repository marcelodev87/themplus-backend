<?php

namespace App\Repositories;

use App\Models\Movement;
use Carbon\Carbon;

class MovementRepository
{
    protected $model;

    public function __construct(Movement $movement)
    {
        $this->model = $movement;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterpriseWithRelations($enterpriseId)
    {
        return $this->model->with(['account', 'category'])
            ->where('enterprise_id', $enterpriseId)
            ->get();
    }

    public function getAllByEnterpriseWithRelationsWithParams($request)
    {
        $entry = $request->has('entry') ? filter_var($request->query('entry'), FILTER_VALIDATE_BOOLEAN) : null;
        $out = $request->has('out') ? filter_var($request->query('out'), FILTER_VALIDATE_BOOLEAN) : null;

        $query = $this->model->with(['account', 'category'])
            ->where('enterprise_id', $request->user()->enterprise_id);

        if (! is_null($out) && $out) {
            $query->where('type', 'saída');
        }

        if (! is_null($entry) && $entry) {
            $query->where('type', 'entrada');
        }

        return $query->get();
    }

    public function export($out, $entry, $enterpriseId)
    {
        $query = $this->model->with(['account', 'category'])
            ->where('enterprise_id', $enterpriseId);

        if ($out) {
            $query->where('type', 'saída');
        }

        if ($entry) {
            $query->where('type', 'entrada');
        }

        return $query->get();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getDeliveries($enterpriseId)
    {
        $distinctMonthsYears = $this->model
            ->where('enterprise_id', $enterpriseId)
            ->selectRaw('DISTINCT DATE_FORMAT(date_movement, "%m/%Y") as month_year')
            ->orderBy('date_movement')
            ->pluck('month_year');

        $results = [];

        foreach ($distinctMonthsYears as $monthYear) {
            [$month, $year] = explode('/', $monthYear);
            $month = (int) $month;
            $year = (int) $year;

            $financialMovement = \DB::table('financial_movements')
                ->where('enterprise_id', $enterpriseId)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if ($financialMovement) {
                $status = true;
                $dateDelivery = $financialMovement->date_delivery;
            } else {
                $status = false;
                $dateDelivery = null;
            }

            $results[] = [
                'month_year' => $monthYear,
                'status' => $status,
                'date_delivery' => $dateDelivery,
            ];
        }

        return $results;
    }

    public function getMonthYears($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)
            ->selectRaw('DATE_FORMAT(date_movement, "%m/%Y") as month_year')
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->pluck('month_year');
    }

    public function getMovementsDashboard($enterpriseId)
    {
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');
        $monthYear = Carbon::now()->format('m/Y');

        $movements = $this->model
            ->where('movements.enterprise_id', $enterpriseId)
            ->whereYear('date_movement', $currentYear)
            ->whereMonth('date_movement', $currentMonth)
            ->join('categories', 'movements.category_id', '=', 'categories.id')
            ->selectRaw('
                SUM(CASE WHEN categories.type = "entrada" THEN movements.value ELSE 0 END) as entry_value,
                SUM(CASE WHEN categories.type = "saida" THEN movements.value ELSE 0 END) as out_value
            ')
            ->first();

        $entryValue = $movements->entry_value ?? 0;
        $outValue = $movements->out_value ?? 0;
        $balance = $entryValue - $outValue;

        return [
            'entry_value' => $entryValue,
            'out_value' => $outValue,
            'balance' => $balance,
            'month_year' => $monthYear,
        ];
    }

    public function getMovementsByCategoriesDashboard($enterpriseId)
    {
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        return $this->model
            ->select('movements.category_id')
            ->selectRaw('SUM(movements.value) as value')
            ->join('categories', 'movements.category_id', '=', 'categories.id')
            ->whereYear('movements.created_at', $currentYear)
            ->whereMonth('movements.created_at', $currentMonth)
            ->where('movements.enterprise_id', $enterpriseId)
            ->groupBy('movements.category_id')
            ->with(['category:id,name,type'])
            ->get()
            ->map(function ($movement) {
                return [
                    'category_id' => $movement->category_id,
                    'name' => $movement->category->name,
                    'type' => $movement->category->type,
                    'value' => $movement->value,
                ];
            });
    }

    public function getAllByAccount($enterpriseId, $accountId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)
            ->where('account_id', $accountId)
            ->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $movement = $this->findById($id);
        if ($movement) {
            $movement->update($data);

            return $movement;
        }

        return null;
    }

    public function delete($id)
    {
        $movement = $this->findById($id);
        if ($movement) {
            return $movement->delete();
        }

        return false;
    }
}
