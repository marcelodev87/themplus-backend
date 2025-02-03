<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
            'created_by' => $this->created_by,
            'active' => $this->active,
            'enterprise_id' => $this->enterprise_id,
            'department_id' => $this->department_id,
            'department' => $this->department_id ? [
                'id' => $this->department->id,
                'name' => $this->department->name,
            ] : null,
        ];
    }
}
