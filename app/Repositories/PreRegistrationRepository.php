<?php

namespace App\Repositories;

use App\Models\PreRegistration;
use Illuminate\Support\Facades\DB;

class PreRegistrationRepository
{
    protected $model;

    public function __construct(PreRegistration $model)
    {
        $this->model = $model;
    }

    public function getAllByEnterprise(string $enterpriseId, array|string|null $relations = null)
    {
        $query = $this->model->where('enterprise_id', $enterpriseId);

        if ($relations) {
            $query->with($relations);
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
        $registration = $this->findById($id);
        if ($registration) {
            $registration->update($data);

            return $registration;
        }

        return null;
    }

    public function delete($id)
    {
        $registration = $this->findById($id);
        if ($registration) {
            DB::table('pre_registration_relationship')
                ->where('pre_registration_id', $id)
                ->delete();

            return $registration->delete();
        }

        return false;
    }
}
