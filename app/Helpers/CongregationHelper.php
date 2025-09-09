<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CongregationHelper
{
    public static function existsCongregation($enterpriseId, $name, $mode, $congregationID = null)
    {
        $existingCongregation = DB::table('congregations')
            ->where('enterprise_id', $enterpriseId)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingCongregation) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe uma congregação com esse nome.'],
                ]);
            }
        } else {
            if ($existingCongregation && $existingCongregation->id !== $congregationID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outra congregação com esse nome.'],
                ]);
            }
        }
    }
}
