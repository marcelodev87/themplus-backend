<?php

namespace App\Repositories;

use App\Models\Congregation;
use Illuminate\Support\Facades\DB;

class CongregationRepository
{
    protected $model;

    public function __construct(Congregation $congregation)
    {
        $this->model = $congregation;
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
        $congregation = $this->findById($id);
        if ($congregation) {
            $congregation->update($data);

            return $congregation;
        }

        return null;
    }

    public function delete($id)
    {
        $congregation = $this->findById($id);
        if ($congregation) {
            DB::table('members')->where('congregation_id', $id)->update(['congregation_id' => null]);
            DB::table('networks')->where('congregation_id', $id)->update(['congregation_id' => null]);
            DB::table('cells')->where('congregation_id', $id)->update(['congregation_id' => null]);

            return $congregation->delete();
        }

        return false;
    }
}
