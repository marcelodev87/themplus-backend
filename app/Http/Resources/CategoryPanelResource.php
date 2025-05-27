<?php

namespace App\Http\Resources;

use App\Models\Enterprise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryPanelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $enterprise = Enterprise::find($this->enterprise_id);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'default' => $this->default,
            'organization_name' => $this->default === 1 ? null : $this->enterprise->name,
            'code_debt' => $this->code_debt,
            'code_credit' => $this->code_credit
        ];
    }
}
