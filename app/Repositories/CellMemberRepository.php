<?php

namespace App\Repositories;

use App\Models\CellMember;

class CellMemberRepository
{
    protected $model;

    public function __construct(CellMember $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise(int $enterpriseId, array|string|null $relations = null)
    {
        $query = $this->model->where('enterprise_id', $enterpriseId);

        if ($relations) {
            $query->with($relations);
        }

        return $query->get();
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
        $cell = $this->findById($id);
        if ($cell) {
            $cell->update($data);

            return $cell;
        }

        return null;
    }

    public function delete($cellID, $memberID)
    {
        return $this->model
            ->where('cell_id', $cellID)
            ->where('member_id', $memberID)
            ->delete();
    }
}
