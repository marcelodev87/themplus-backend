<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Rules\AlertRule;

class AlertService
{
    protected $rule;

    protected $repository;

    public function __construct(
        AlertRule $rule,
        CategoryRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = $request->only(['description']);
        $data['enterprise_id'] = $request->user()->enterprise_id;

        return $this->repository->create($data);
    }

    public function update($request)
    {
        foreach ($request->input('categories') as $categorie) {

            $data = ['alert' => $categorie['alert']];
            $this->repository->update($categorie['id'], $data);
        }

    }
}
