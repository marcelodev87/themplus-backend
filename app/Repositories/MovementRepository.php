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

    public function getAllByEnterpriseWithRelationsWithParamsByDate($request, $date)
    {
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

    public function export($out, $entry, $date, $enterpriseId)
    {
        $query = $this->model->with(['account', 'category'])
            ->where('enterprise_id', $enterpriseId);

        if ($out) {
            $query->where('type', 'saída');
        }

        if ($entry) {
            $query->where('type', 'entrada');
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

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getAllByCategory($categoryId)
    {
        return $this->model->where('category_id', $categoryId)->get();
    }

    public function getDeliveries($enterpriseId)
    {
        try {
            $monthsYears = $this->model
                ->where('enterprise_id', $enterpriseId)
                ->selectRaw("DATE_FORMAT(date_movement, '%Y-%m') as year_month")
                ->groupBy('year_month')
                ->orderBy('year_month', 'ASC')
                ->pluck('year_month')
                ->toArray();

            if (empty($monthsYears)) {
                return [];
            }

            $financialMovements = \DB::table('financial_movements')
                ->where('enterprise_id', $enterpriseId)
                ->whereIn(
                    \DB::raw("DATE_FORMAT(date_movement, '%Y-%m')"),
                    $monthsYears
                )
                ->get()
                ->keyBy(fn ($fm) => date('Y-m', strtotime($fm->date_movement)));

            $results = array_map(function ($yearMonth) use ($financialMovements) {
                [$year, $month] = explode('-', $yearMonth);
                $key = "$year-$month";
                $financialMovement = $financialMovements[$key] ?? null;

                return [
                    'month_year' => (int) $month.'/'.$year,
                    'status' => (bool) $financialMovement,
                    'date_delivery' => $financialMovement->date_delivery ?? null,
                ];
            }, $monthsYears);

            return $results;

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar entregas: '.$e->getMessage());

            return [];
        }
    }

    public function getMonthYears($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)
            ->selectRaw('DATE_FORMAT(date_movement, "%m/%Y") as month_year')
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->pluck('month_year');
    }

    public function getMovementsDashboard($enterpriseId, $date)
    {

        $carbonDate = Carbon::createFromFormat('m-Y', $date);

        $month = $carbonDate->month;
        $year = $carbonDate->year;

        $movements = $this->model
            ->where('movements.enterprise_id', $enterpriseId)
            ->whereYear('date_movement', $year)
            ->whereMonth('date_movement', $month)
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
            'month_year' => $date,
        ];
    }

    public function getMovementsByCategoriesDashboard($enterpriseId, $date)
    {
        $carbonDate = Carbon::createFromFormat('m-Y', $date);

        $month = $carbonDate->month;
        $year = $carbonDate->year;

        return $this->model
            ->select('movements.category_id')
            ->selectRaw('SUM(movements.value) as value')
            ->join('categories', 'movements.category_id', '=', 'categories.id')
            ->whereYear('movements.date_movement', $year)
            ->whereMonth('movements.date_movement', $month)
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

    public function getAllByAccount($accountId)
    {
        return $this->model->where('account_id', $accountId)->get();
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
