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
        $months_years = $this->movementRepository->getMonthYears($enterpriseId);
        $categories_dashboard = $this->movementRepository->getMovementsByCategoriesDashboard($enterpriseId);
        $movements_dashboard = $this->movementRepository->getMovementsDashboard($enterpriseId);
        $users_dashboard = $this->userRepository->getUsersDashboard($enterpriseId);
        $schedulings_dashboard = $this->schedulingRepository->getSchedulingsDashboard($enterpriseId);
        $accounts_dashboard = $this->accountRepository->getAccountsDashboard($enterpriseId);

        return [
            'months_years' => $months_years,
            'categories_dashboard' => $categories_dashboard,
            'movements_dashboard' => $movements_dashboard,
            'users_dashboard' => $users_dashboard,
            'schedulings_dashboard' => $schedulings_dashboard,
            'accounts_dashboard' => $accounts_dashboard,
        ];
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