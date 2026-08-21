<?php

namespace App\Repositories;

use App\Models\CounterClientLicense;

class CounterClientLicenseRepository
{
    protected $model;

    public function __construct(CounterClientLicense $counterClientLicense)
    {
        $this->model = $counterClientLicense;
    }

    public function countActiveByCounter($counterEnterpriseId)
    {
        return $this->model->where('counter_enterprise_id', $counterEnterpriseId)
            ->where('active', true)
            ->count();
    }

    public function getActiveByCounter($counterEnterpriseId)
    {
        return $this->model->with('clientEnterprise')
            ->where('counter_enterprise_id', $counterEnterpriseId)
            ->where('active', true)
            ->orderBy('granted_at')
            ->get();
    }

    public function findActiveByClient($clientEnterpriseId)
    {
        return $this->model->where('client_enterprise_id', $clientEnterpriseId)
            ->where('active', true)
            ->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function revoke($id)
    {
        $license = $this->model->find($id);
        if ($license) {
            $license->update(['active' => false, 'revoked_at' => now()]);

            return $license;
        }

        return null;
    }
}
