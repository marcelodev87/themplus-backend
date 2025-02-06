<?php

namespace App\Repositories;

use App\Models\Register;
use Carbon\Carbon;

class RegisterRepository
{
    protected $model;

    public function __construct(Register $register)
    {
        $this->model = $register;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId, $period)
    {
        $query = $this->model->with('user', 'enterprise')
            ->where('enterprise_id', $enterpriseId);

        switch ($period) {
            case 'all':
                break;

            case 'today':
                $today = Carbon::now()->format('d-m-Y');
                $query->where('date_register', 'like', "$today%");
                break;

            case 'yesterday':
                $yesterday = Carbon::yesterday()->format('d-m-Y');
                $query->where('date_register', 'like', "$yesterday%");
                break;

            case 'last15':
                $last15Days = Carbon::now()->subDays(15)->format('Y-m-d H:i:s');
                $query->whereRaw("STR_TO_DATE(date_register, '%d-%m-%Y %H:%i:%s') >= ?", [$last15Days]);
                break;

            case 'last30':
                $last30Days = Carbon::now()->subDays(30)->format('Y-m-d H:i:s');
                $query->whereRaw("STR_TO_DATE(date_register, '%d-%m-%Y %H:%i:%s') >= ?", [$last30Days]);
                break;

            default:
                throw new \InvalidArgumentException("Período inválido: $period");
        }

        return $query->get();
    }

    public function getAllByEnterpriseAndUser($enterpriseId, $userId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)
            ->where('user_id', $userId)
            ->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
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
