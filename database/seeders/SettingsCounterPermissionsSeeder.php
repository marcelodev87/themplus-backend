<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsCounterPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings_counter')->update([
            'allow_edit_movement' => 1,
            'allow_delete_movement' => 1,
        ]);
    }
}
