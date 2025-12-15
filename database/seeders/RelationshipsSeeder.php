<?php

namespace Database\Seeders;

use App\Models\Relationship;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RelationshipsSeeder extends Seeder
{
    public function run(): void
    {
        $enterprises = DB::table('enterprises')->select('id')->get();

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

        foreach ($enterprises as $enterprise) {
            foreach ($relationships as $data) {
                Relationship::firstOrCreate(
                    [
                        'name' => $data['name'],
                        'enterprise_id' => $enterprise->id,
                    ],
                    [
                        'default' => $data['default'],
                    ]
                );
            }
        }
    }
}
