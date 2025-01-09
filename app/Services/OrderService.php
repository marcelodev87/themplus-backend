<?php

namespace App\Services;

use App\Repositories\EnterpriseRepository;
use App\Repositories\OrderRepository;
use App\Rules\OrderRule;

class OrderService
{
    protected $rule;

    protected $repository;

    protected $enterpriseRepository;

    public function __construct(
        OrderRule $rule,
        OrderRepository $repository,
        EnterpriseRepository $enterpriseRepository
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->enterpriseRepository = $enterpriseRepository;
    }

    public function create($request)
    {
        $this->rule->create($request);
        $this->checkLimitOrders($request->input('enterpriseId'));
        $this->checkExists($request->input('enterpriseId'), $request->user()->enterprise_id);

        $data = [
            'enterprise_id' => $request->input('enterpriseId'),
            'enterprise_counter_id' => $request->user()->enterprise_id,
            'description' => $request->input('description'),
        ];

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        return $this->repository->update($request->input('id'), ['description' => $request->input('description')]);
    }

    public function bindCounter($request)
    {
        if ($request->input('status') === 'accepted') {
            $order = $this->repository->findById($request->input('id'));
            $offices = $this->enterpriseRepository->getAllOfficesByEnterprise($request->user()->enterprise_id);

            $this->enterpriseRepository->update($request->user()->enterprise_id, ['counter_enterprise_id' => $order->enterprise_counter_id]);

            foreach ($offices as $office) {
                $office->update(['counter_enterprise_id' => $order->enterprise_counter_id]);
            }
        }
        $this->repository->delete($request->input('id'));

    }

    public function checkLimitOrders($enterpriseId)
    {
        $results = $this->repository->getAllByUser($enterpriseId);

        if (count($results) >= 10) {
            throw new \Exception('Não foi possível finalizar a solicitação, pois a organização solicitada está com a caixa de solicitações cheia');
        }
    }

    public function checkExists($enterpriseId, $enterpriseCounterId)
    {
        $results = $this->repository->checkExistOrders($enterpriseId, $enterpriseCounterId);

        if (count($results) >= 1) {
            throw new \Exception('Já existe uma solicitação para esta organização');
        }
    }
}
