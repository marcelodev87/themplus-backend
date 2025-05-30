<?php

namespace App\Http\Resources\EnterpriseCoupons;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnterpriseCouponListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'account_number' => $this->account_number,
            'agency_number' => $this->agency_number,
        ];
    }
}
