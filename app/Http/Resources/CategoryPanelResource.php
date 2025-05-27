<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryPanelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->default === 1 ? null : $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'default' => $this->default,
            'organization_name' => $this->default === 1 ? null : $this->enterprise->name,
            'code_debt' => $this->code_debt,
            'code_credit' => $this->code_credit,
        ];
    }
}
