<?php

namespace App\Http\Resources;

use App\Models\Enterprise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfficeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $enterpriseName = $this->counter_enterprise_id ? Enterprise::find($this->counter_enterprise_id)?->name : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'users' => $this->users,
            'counter' => $enterpriseName,
        ];
    }
}
