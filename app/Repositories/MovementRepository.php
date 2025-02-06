<?php

namespace App\Repositories;

use App\Models\Movement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function checkDelivered($enterpriseId, $date)
    {
        [$month, $year] = explode('-', $date);

        $exists = DB::table('financial_movements')
            ->where('enterprise_id', $enterpriseId)
            ->where('year', $year)
            ->where('month', $month)
            ->exists();

        return $exists;
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

    public function getAllByEnterpriseWithRelationsByDateWithObservation($enterpriseId, $date)
    {
        $query = $this->model->with(['account', 'category'])
            ->where('enterprise_id', $enterpriseId);

        [$month, $year] = explode('-', $date);

        if (! is_numeric($month) || ! is_numeric($year) || strlen($month) !== 2 || strlen($year) !== 4) {
            return collect();
        }

        $query->whereMonth('date_movement', $month)
            ->whereYear('date_movement', $year)
            ->whereNotNull('observation');

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
        $categoryId = ($request->query('category') === 'null') ? null : $request->query('category');

        $query = $this->model->with(['account', 'category'])
            ->where('enterprise_id', $request->user()->view_enterprise_id);

        if ($out !== null && $out) {
            $query->where('type', 'saída');
        }

        if ($entry !== null && $entry) {
            $query->where('type', 'entrada');
        }

        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
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

    public function export($out, $entry, $date, $categoryId, $enterpriseId)
    {
        $query = $this->model->with(['account', 'category'])
            ->where('enterprise_id', $enterpriseId);

        if ($out) {
            $query->where('type', 'saída');
        }

        if ($entry) {
            $query->where('type', 'entrada');
        }

        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
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

    public function getGeneralByEnteprise($enterpriseId)
    {
        $movements = $this->model->where('enterprise_id', $enterpriseId)->get();

        $entrySum = 0;
        $outSum = 0;

        foreach ($movements as $movement) {
            if ($movement->type === 'entrada') {
                $entrySum += $movement->value;
            } else {
                $outSum += $movement->value;
            }
        }

        return [
            'entry' => $entrySum,
            'out' => $outSum,
        ];
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
                ->whereNotNull('date_movement')
                ->get(['date_movement'])
                ->pluck('date_movement')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m');
                })
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            if (empty($monthsYears)) {
                return [];
            }
            $resultArray = [];

            foreach ($monthsYears as $monthYear) {
                [$year, $month] = explode('-', $monthYear);

                $financialRecords = DB::table('financial_movements')
                    ->where('enterprise_id', $enterpriseId)
                    ->where('year', $year)
                    ->where('month', $month)
                    ->get();

                $status = false;
                $dateDelivery = null;
                $hasObservation = false;

                if ($financialRecords->isNotEmpty()) {
                    $status = true;
                    $dateDelivery = $financialRecords->first()->date_delivery;
                }

                $movements = DB::table('movements')
                    ->whereYear('date_movement', $year)
                    ->whereMonth('date_movement', $month)
                    ->get();

                foreach ($movements as $movement) {
                    if ($movement->observation !== null) {
                        $hasObservation = true;
                        break;
                    }
                }

                $resultArray[] = [
                    'month_year' => "$month/$year",
                    'status' => $status,
                    'date_delivery' => $dateDelivery,
                    'has_observation' => $hasObservation,
                ];
            }

            return $resultArray;

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

    public function getMovementsDashboard($enterpriseId, $date, $mode)
    {
        \Log::info($date);
        $query = $this->model
            ->where('movements.enterprise_id', $enterpriseId)
            ->join('categories', 'movements.category_id', '=', 'categories.id')
            ->selectRaw('
            SUM(CASE WHEN categories.type = "entrada" THEN movements.value ELSE 0 END) as entry_value,
            SUM(CASE WHEN categories.type = "saida" THEN movements.value ELSE 0 END) as out_value'
            );

        if ($mode === 'month') {
            $carbonDate = Carbon::createFromFormat('m-Y', $date);
            $month = $carbonDate->month;
            $year = $carbonDate->year;

            $query->whereYear('movements.date_movement', $year)
                ->whereMonth('movements.date_movement', $month);
        } else {

            $fromDate = str_replace('/', '-', $date['from']);
            $toDate = str_replace('/', '-', $date['to']);

            $from = Carbon::createFromFormat('Y-m-d', $fromDate);
            $to = Carbon::createFromFormat('Y-m-d', $toDate);

            $query->whereBetween('movements.date_movement', [$from, $to]);
        }

        $movements = $query->first();

        $entryValue = $movements->entry_value ?? 0;
        $outValue = $movements->out_value ?? 0;
        $balance = $entryValue - $outValue;

        return [
            'entry_value' => $entryValue,
            'out_value' => $outValue,
            'balance' => $balance,
            'month_year' => $mode === 'month' ? $date : null,
            'period' => $mode === 'period' ? $date : null,
        ];
    }

    public function getMovementsByCategoriesDashboard($enterpriseId, $date, $mode, $category)
    {
        $query = $this->model
            ->select('movements.category_id')
            ->selectRaw('SUM(movements.value) as value')
            ->join('categories', 'movements.category_id', '=', 'categories.id')
            ->where('movements.enterprise_id', $enterpriseId)
            ->groupBy('movements.category_id')
            ->with(['category:id,name,type']);

        if ($mode === 'month') {
            $carbonDate = Carbon::createFromFormat('m-Y', $date);
            $month = $carbonDate->month;
            $year = $carbonDate->year;

            $query->whereYear('movements.date_movement', $year)
                ->whereMonth('movements.date_movement', $month);
        } else {

            $from = Carbon::createFromFormat('Y-m/d', $date['from']);
            $to = Carbon::createFromFormat('Y-m/d', $date['to']);

            $query->whereBetween('movements.date_movement', [$from, $to]);
        }

        if ($category !== null) {
            $query->where('movements.category_id', $category);
        }

        return $query->get()
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

    public function clearObservations($enterpriseId, $month, $year)
    {
        $this->model->where('enterprise_id', $enterpriseId)
            ->whereYear('date_movement', $year)
            ->whereMonth('date_movement', $month)
            ->update(['observation' => null]);
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
