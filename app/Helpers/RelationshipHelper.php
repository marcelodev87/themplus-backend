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
            ['name' => 'Pai', 'default' => 1],
            ['name' => 'Mãe', 'default' => 1],
            ['name' => 'Avô', 'default' => 1],
            ['name' => 'Avó', 'default' => 1],
            ['name' => 'Filho', 'default' => 1],
            ['name' => 'Filha', 'default' => 1],
            ['name' => 'Neto', 'default' => 1],
            ['name' => 'Neta', 'default' => 1],
            ['name' => 'Irmão', 'default' => 1],
            ['name' => 'Irmã', 'default' => 1],
            ['name' => 'Cunhado', 'default' => 1],
            ['name' => 'Cunhada', 'default' => 1],
            ['name' => 'Cônjuge', 'default' => 1],
            ['name' => 'Companheiro(a)', 'default' => 1],
            ['name' => 'Tio', 'default' => 1],
            ['name' => 'Tia', 'default' => 1],
            ['name' => 'Primo', 'default' => 1],
            ['name' => 'Prima', 'default' => 1],
            ['name' => 'Sobrinho', 'default' => 1],
            ['name' => 'Sobrinha', 'default' => 1],
            ['name' => 'Sogro', 'default' => 1],
            ['name' => 'Sogra', 'default' => 1],
            ['name' => 'Padrinho', 'default' => 1],
            ['name' => 'Madrinha', 'default' => 1],
            ['name' => 'Amigo(a)', 'default' => 1],
            ['name' => 'Conhecido(a)', 'default' => 1],
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
