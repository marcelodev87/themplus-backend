<?php

namespace App\Repositories;

use App\Models\Ministry;
use Illuminate\Support\Facades\DB;

class MinistryRepository
{
    protected $model;

    public function __construct(Ministry $model)
    {
        $this->model = $model;
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
        $ministry = $this->findById($id);
        if ($ministry) {
            $ministry->update($data);

            return $ministry;
        }

        return null;
    }

    public function delete($id)
    {
        $ministry = $this->findById($id);
        if ($ministry) {
            DB::table('ministry_members')->where('ministry_id', $id)->delete();
            
            return $ministry->delete();
        }

        return false;
    }
}
