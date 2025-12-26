<?php

namespace Database\Seeders;

use App\Models\PreRegistrationConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreRegistrationConfigSeeder extends Seeder
{
    public function run(): void
    {
        $enterprises = DB::table('enterprises')
            ->where('position', 'client')
            ->select('id')
            ->get();

        foreach ($enterprises as $enterprise) {
            PreRegistrationConfig::firstOrCreate(
                ['enterprise_id' => $enterprise->id],
                ['active' => 0]
            );
        }
    }
}
