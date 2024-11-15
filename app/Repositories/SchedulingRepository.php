<?php

namespace App\Repositories;

use App\Models\Scheduling;
use App\Services\MovementService;
use Carbon\Carbon;

class SchedulingRepository
{
    protected $model;

    protected $movementService;

    public function __construct(Scheduling $scheduling, MovementService $movementService)
    {
        $this->model = $scheduling;
        $this->movementService = $movementService;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function getAllByEnterpriseWithRelations($enterpriseId)
    {
        return $this->model->with(['account', 'category'])
            ->where('enterprise_id', $enterpriseId)
            ->get();
    }

    public function getAllByEnterpriseWithRelationsWithParams($request)
    {
        $expired = $request->has('expired') ? filter_var($request->query('expired'), FILTER_VALIDATE_BOOLEAN) : null;
        $entry = $request->has('entry') ? filter_var($request->query('entry'), FILTER_VALIDATE_BOOLEAN) : null;
        $out = $request->has('out') ? filter_var($request->query('out'), FILTER_VALIDATE_BOOLEAN) : null;

        $query = $this->model->with(['account', 'category'])
            ->where('enterprise_id', $request->user()->enterprise_id);

        if (! is_null($out) && $out) {
            $query->where('type', 'saida');
        }

        if (! is_null($entry) && $entry) {
            $query->where('type', 'entrada');
        }

        if (! is_null($expired) && $expired) {
            $yesterday = Carbon::yesterday()->format('Y-m-d');
            $query->where('date_movement', '<', $yesterday);
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
        $scheduling = $this->findById($id);
        if ($scheduling) {
            return $scheduling->update($data);
        }

        return null;
    }

    public function finalize($id)
    {
        $scheduling = $this->findById($id);
        if ($scheduling) {
            $result = $this->movementService->includeScheduling($scheduling->toArray());
            if ($result) {
                return $scheduling->delete();
            }

        }

        return null;
    }

    public function delete($id)
    {
        $scheduling = $this->findById($id);
        if ($scheduling) {
            return $scheduling->delete();
        }

        return false;
    }
}
