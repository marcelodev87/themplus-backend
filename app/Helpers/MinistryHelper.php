<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MinistryHelper
{
    public static function existsNetwork($enterpriseId, $name, $mode, $ministryID = null)
    {
        $existingMinistry= DB::table('ministries')
            ->where('enterprise_id', $enterpriseId)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingMinistry) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe um ministério com esse nome.'],
                ]);
            }
        } else {
            if ($existingMinistry && $existingMinistry->id !== $ministryID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outro ministério com esse nome.'],
                ]);
            }
        }
    }
}
