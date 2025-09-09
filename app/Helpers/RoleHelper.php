<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoleHelper
{
    public static function existsRole($enterpriseId, $name, $mode, $roleID = null)
    {
        $existingRole = DB::table('roles')
            ->where('enterprise_id', $enterpriseId)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingRole) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe um cargo com esse nome.'],
                ]);
            }
        } else {
            if ($existingRole && $existingRole->id !== $roleID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outro cargo com esse nome.'],
                ]);
            }
        }
    }
}
