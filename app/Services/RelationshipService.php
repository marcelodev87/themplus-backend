<?php

namespace App\Services;

use App\Helpers\RelationshipHelper;
use App\Repositories\RelationshipRepository;
use App\Rules\RelationshipRule;
use Illuminate\Validation\ValidationException;

class RelationshipService
{
    protected $rule;

    protected $repository;

    public function __construct(
        RelationshipRule $rule,
        RelationshipRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        RelationshipHelper::existsRelationship(
            $request->user()->enterprise_id,
            $request->input('name'),
            'create',
        );

        $data = $request->only(['name']);
        $data['enterprise_id'] = $request->user()->enterprise_id;

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $relationship = $this->repository->findById($request->input('id'));
        if ($relationship->default === 1) {
            throw ValidationException::withMessages([
                'default' => ['Não pode atualizar relação padrão do sistema'],
            ]);
        }
        $this->rule->update($request);

        RelationshipHelper::existsRelationship(
            $request->user()->enterprise_id,
            $request->input('name'),
            'update',
            $request->input('id')
        );

        $data = $request->only(['name']);

        return $this->repository->update($request->input('id'), $data);
    }
}
