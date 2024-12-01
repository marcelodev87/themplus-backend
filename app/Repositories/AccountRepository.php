<?php

namespace App\Repositories;

use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountRepository
{
    protected $model;

    public function __construct(Account $account)
    {
        $this->model = $account;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getAllByEnterpriseOnlyActive($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->where('active', 1)->get();
    }

    public function getAccountsDashboard($enterpriseId)
    {
        $monthYear = Carbon::now()->format('m/Y');

        $accounts = $this->model
            ->where('enterprise_id', $enterpriseId)
            ->orderBy('balance', 'desc')
            ->take(3)
            ->get(['name', 'balance'])
            ->map(function ($account) {
                return [
                    'name' => $account->name,
                    'balance' => (float) $account->balance,
                ];
            });

        return [
            'month_year' => $monthYear,
            'accounts' => $accounts->toArray(),
        ];
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByName($name, $enterpriseId)
    {
        return $this->model
            ->where(DB::raw('LOWER(name)'), '=', strtolower($name))
            ->where('enterprise_id', $enterpriseId)
            ->first();
    }

    public function findByParams($name, $enterpriseId, $agencyNumber, $accountNumber)
    {
        return $this->model
            ->where(DB::raw('LOWER(name)'), '=', strtolower($name))
            ->where('enterprise_id', $enterpriseId)
            ->where('agency_number', $agencyNumber)
            ->where('account_number', $accountNumber)
            ->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $account = $this->findById($id);
        if ($account) {
            $account->update($data);

            return $account;
        }

        return null;
    }

    public function updateBalance($accountId, $newBalance)
    {
        $account = $this->findById($accountId);

        if ($account) {
            $account->update(['balance' => $newBalance]);

            return $account;
        }

        return null;
    }

    public function delete($id)
    {
        $account = $this->findById($id);
        if ($account) {
            return $account->delete();
        }

        return false;
    }
}
