<?php

namespace App\Repositories;

use App\Models\MovementAnalyze;

class MovementAnalyzeRepository
{
    protected $model;

    public function __construct(MovementAnalyze $model)
    {
        $this->model = $model;
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getAllByEnterpriseWithRelations($enterpriseId)
    {
        $results = $this->model->where('enterprise_id', $enterpriseId)
            ->with(['category:id,name', 'account:id,name'])
            ->get()
            ->map(function ($item) {
                $data = $item->toArray();
                if (isset($data['category'])) {
                    $data['category'] = [
                        'value' => $data['category']['id'],
                        'label' => $data['category']['name'],
                    ];
                }
                if (isset($data['account'])) {
                    $data['account'] = [
                        'value' => $data['account']['id'],
                        'label' => $data['account']['name'],
                    ];
                }

                return $data;
            });

        return $results;

    }

    public function countByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->count();
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
