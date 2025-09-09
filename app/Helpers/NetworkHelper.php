<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NetworkHelper
{
    public static function existsNetwork($enterpriseId, $name, $mode, $networkID = null)
    {
        $existingNetwork = DB::table('networks')
            ->where('enterprise_id', $enterpriseId)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingNetwork) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe uma rede com esse nome.'],
                ]);
            }
        } else {
            if ($existingNetwork && $existingNetwork->id !== $networkID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outra rede com esse nome.'],
                ]);
            }
        }
    }
}
