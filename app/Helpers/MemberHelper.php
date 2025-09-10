<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MemberHelper
{
    public static function existsMember($enterpriseId, $name, $mode, $memberID = null)
    {
        $existingMember = DB::table('members')
            ->where('enterprise_id', $enterpriseId)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingMember) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe um membro com esse nome.'],
                ]);
            }
        } else {
            if ($existingMember && $existingMember->id !== $memberID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outro membro com esse nome.'],
                ]);
            }
        }
    }
}
