<?php

namespace App\Services;

use App\Helpers\CategoryHelper;
use App\Repositories\CategoryRepository;
use App\Repositories\MovementRepository;
use App\Repositories\SchedulingRepository;
use App\Rules\CategoryRule;

class CategoryService
{
    protected $rule;

    protected $repository;

    protected $movementRepository;

    protected $schedulingRepository;

    public function __construct(
        CategoryRule $rule,
        CategoryRepository $repository,
        MovementRepository $movementRepository,
        SchedulingRepository $schedulingRepository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->movementRepository = $movementRepository;
        $this->schedulingRepository = $schedulingRepository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        CategoryHelper::existsCategory(
            $request->input('name'),
            $request->input('type'),
            $request->user()->enterprise_id
        );

        $data = $request->only(['name', 'type']);
        $data['enterprise_id'] = $request->user()->enterprise_id;
        $data['alert_id'] = $request->input('alert');

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        CategoryHelper::existsCategory(
            $request->input('name'),
            $request->input('type'),
            $request->user()->enterprise_id
        );

        $data = $request->only(['name', 'type']);
        $data['enterprise_id'] = $request->user()->enterprise_id;
        $data['alert_id'] = $request->input('alert');

        return $this->repository->update($request->input('id'), $data);
    }

    public function updateActive($id)
    {
        $data['active'] = 1;

        return $this->repository->update($id, $data);

    }

    public function delete($id)
    {
        $movements = $this->movementRepository->getAllByCategory($id);
        $schedulings = $this->schedulingRepository->getAllByCategory($id);

        if ($movements->isNotEmpty() || $schedulings->isNotEmpty()) {
            $data['active'] = 0;
            $result = $this->repository->update($id, $data);

            return [
                'message' => 'Categoria inativada, pois possui movimentações ou agendamentos vinculados',
                'data' => $result,
                'inactivated' => true,
            ];
        } else {
            $result = $this->repository->delete($id);

            return [
                'message' => 'Categoria deletada com sucesso',
                'data' => $result,
                'inactivated' => false,
            ];
        }
    }
}
