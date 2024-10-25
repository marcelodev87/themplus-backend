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

    public function delete($id)
    {
        $account = $this->model->find($id);
        if ($account) {
            return $account->delete();
        }

        return false;
    }
}
