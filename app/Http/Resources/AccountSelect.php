<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountSelect extends JsonResource
{
    public function toArray(Request $request): array
    {
        $parts = [$this->name];

        if (! empty($this->account_number)) {
            $parts[] = "C: {$this->account_number}";
        }

        if (! empty($this->agency_number)) {
            $parts[] = "Ag: {$this->agency_number}";
        }

        return [
            'value' => $this->id,
            'label' => implode(' | ', $parts),
        ];
    }
}
