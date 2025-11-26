<?php

namespace Database\Seeders;

use App\Models\Relationship;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RelationshipsSeeder extends Seeder
{
    public function run()
    {
        $enterprises = DB::table('enterprises')->select('id')->get();

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

        foreach ($enterprises as $enterprise) {
            foreach ($relationships as $data) {
                Relationship::create([
                    'name' => $data['name'],
                    'enterprise_id' => $enterprise->id,
                    'default' => $data['default'] ?? 0,
                ]);
            }
        }
    }
}
