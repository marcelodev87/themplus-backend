<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Rap2hpoutre\FastExcel\FastExcel;

class MovementExport
{
    protected $movements;

    public function __construct(Collection $movements)
    {
        $this->movements = $movements;
    }

    public function download($fileName)
    {
        return (new FastExcel($this->movements))->download($fileName, function ($movement) {
            return [
                'ID' => $movement->id,
                'TYPE' => $movement->type,
                'VALUE' => $movement->value,
                'DATE_MOVEMENT' => $movement->date_movement,
                'DESCRIPTION' => $movement->description,
                'CATEGORY' => $movement->category->name ?? '',
                'ACCOUNT' => $movement->account->name ?? '',
                'CREATED_AT' => $movement->created_at->format('Y-m-d H:i:s'),
                'UPDATED_AT' => $movement->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}
