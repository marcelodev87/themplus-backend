<?php

namespace Database\Seeders;

use App\Models\Enterprise;
use App\Models\SettingsCounter;
use Illuminate\Database\Seeder;

class SettingsCounterSeeder extends Seeder
{
    public function run()
    {
        $enterprises = Enterprise::where('position', 'client')->get();

        foreach ($enterprises as $enterprise) {
            $exists = SettingsCounter::where('enterprise_id', $enterprise->id)->exists();

            if (! $exists) {
                SettingsCounter::create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'enterprise_id' => $enterprise->id,
                    'allow_add_user' => 1,
                    'allow_edit_user' => 1,
                    'allow_delete_user' => 1,
                    'allow_edit_movement' => 0,
                    'allow_delete_movement' => 0,
                ]);
            }
        }
    }

    public function rollback()
    {
        SettingsCounter::truncate();
    }
}
