<?php

namespace App\Repositories;

use App\Models\EnterpriseHasCoupon;
use App\Repositories\External\CouponExternalRepository;

class EnterpriseHasCouponRepository
{
    protected $model;

    protected $couponExternalRepository;

    public function __construct(EnterpriseHasCoupon $model, CouponExternalRepository $couponExternalRepository)
    {
        $this->model = $model;
        $this->couponExternalRepository = $couponExternalRepository;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        $data = $this->model->where('enterprise_id', $enterpriseId)->get();

        foreach ($data as $item) {
            $item->coupon = $this->couponExternalRepository->findById($item->coupon_id);
        }

        return $data;
    }

    public function countCouponUsing($id)
    {
        return $this->model->where('coupon_id', $id)->count();
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
        $coupon = $this->findById($id);
        if ($coupon) {
            $coupon->update($data);

            return $coupon;
        }

        return null;
    }

    public function delete($id)
    {
        $coupon = $this->findById($id);
        if ($coupon) {
            return $coupon->delete();
        }

        return false;
    }
}
