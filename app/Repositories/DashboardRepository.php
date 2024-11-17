<?php

namespace App\Repositories;

class DashboardRepository
{
    protected $movementRepository;

    protected $categoryRepository;

    protected $userRepository;

    protected $accountRepository;

    protected $schedulingRepository;

    public function __construct(
        MovementRepository $movementRepository,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository,
        AccountRepository $accountRepository,
        SchedulingRepository $schedulingRepository,
    ) {
        $this->movementRepository = $movementRepository;
        $this->categoryRepository = $categoryRepository;
        $this->userRepository = $userRepository;
        $this->accountRepository = $accountRepository;
        $this->schedulingRepository = $schedulingRepository;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function mountDashboard($enterpriseId)
    {
        // TODO: CRIAR TODO O DATA PARA ALIMENTAR DASHBOARD NO FRONTEND
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
        $alert = $this->findById($id);
        if ($alert) {
            $alert->update($data);

            return $alert;
        }

        return null;
    }

    public function delete($id)
    {
        $alert = $this->findById($id);
        if ($alert) {
            return $alert->delete();
        }

        return false;
    }
}
