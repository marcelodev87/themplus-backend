<?php

namespace App\Repositories;

use App\Models\Account;
use Carbon\Carbon;

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

    public function getAccountsDashboard($enterpriseId)
    {
        $monthYear = Carbon::now()->format('m/Y');

        $accounts = $this->model
            ->where('enterprise_id', $enterpriseId)
            ->orderBy('balance', 'desc')
            ->take(5)
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
