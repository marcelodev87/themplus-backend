<?php

namespace App\Http\Resources\EnterpriseCoupons;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponEnterpriseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->coupons->name,
            'type' => $this->coupons->type,
            'date_expiration' => $this->coupons->date_expiration,
            'discount' => $this->coupons->discount,
        ];
    }
}
