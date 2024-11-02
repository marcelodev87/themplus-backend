<?php

namespace App\Repositories;

use App\Models\Account;

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
        $account = $this->model->find($id);
        if ($account) {
            $account->update($data);

            return $account;
        }

        return null;
    }

    public function updateBalance($accountId, $type, $value)
    {
        $account = $this->model->find($id);
        if ($account) {
            if ($type === 'entrada') {
                $data = ['balance' => $account->balance + $value];
                $account->update($data);
            } else {
                $data = ['balance' => $account->balance - $value];
                $account->update($data);
            }

            return $account;
        }

        return null;
    }

    public function delete($id)
    {
        $account = $this->model->find($id);
        if ($account) {
            return $account->delete();
        }

        return false;
    }
}
