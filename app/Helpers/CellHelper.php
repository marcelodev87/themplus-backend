<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CellHelper
{
    public static function existsCell($enterpriseId, $name, $mode, $cellID = null)
    {
        $existingCell = DB::table('cells')
            ->where('enterprise_id', $enterpriseId)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingCell) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe uma célula com esse nome.'],
                ]);
            }
        } else {
            if ($existingCell && $existingCell->id !== $cellID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outra célula com esse nome.'],
                ]);
            }
        }
    }
}
