<?php

namespace App\Helpers;

use App\Models\Relationship;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RelationshipHelper
{
    public static function existsRelationship($enterpriseId, $name, $mode, $relationshipID = null)
    {
        $existingRole = DB::table('relationships')
            ->where('enterprise_id', $enterpriseId)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingRole) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe uma relação com esse nome.'],
                ]);
            }
        } else {
            if ($existingRole && $existingRole->id !== $relationshipID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe uma relação com esse nome.'],
                ]);
            }
        }
    }

    public static function createDefault($enterpriseId)
    {
        $relationships = [
            ['name' => 'Avô (ó)', 'default' => 1],
            ['name' => 'Bisavô (ó)', 'default' => 1],
            ['name' => 'Bisneto (a)', 'default' => 1],
            ['name' => 'Cônjuge', 'default' => 1],
            ['name' => 'Cunhado (a)', 'default' => 1],
            ['name' => 'Enteado (a)', 'default' => 1],
            ['name' => 'Filho (a)', 'default' => 1],
            ['name' => 'Genro', 'default' => 1],
            ['name' => 'Irmão (ã)', 'default' => 1],
            ['name' => 'Mãe', 'default' => 1],
            ['name' => 'Namorado(a)', 'default' => 1],
            ['name' => 'Neto (a)', 'default' => 1],
            ['name' => 'Noivo(a)', 'default' => 1],
            ['name' => 'Nora', 'default' => 1],
            ['name' => 'Pai', 'default' => 1],
            ['name' => 'Primo (a)', 'default' => 1],
            ['name' => 'Sobrinho (a)', 'default' => 1],
            ['name' => 'Sogro (a)', 'default' => 1],
            ['name' => 'Tataraneto (a)', 'default' => 1],
            ['name' => 'Tio (a)', 'default' => 1],
            ['name' => 'Trisavô (ó)', 'default' => 1],
        ];

        foreach ($relationships as $relationship) {
            Relationship::create([
                'name' => $relationship['name'],
                'enterprise_id' => $enterpriseId,
                'default' => 1,
            ]);
        }
    }
}
