<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $enterprises = DB::table('members')
            ->orderBy('enterprise_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('enterprise_id');

        foreach ($enterprises as $enterpriseId => $members) {
            $counter = 1;

            foreach ($members as $member) {
                DB::table('members')
                    ->where('id', $member->id)
                    ->update(['registration' => $counter]);
                $counter++;
            }
        }

        $this->command->info('Matrículas populadas com sucesso!');
    }
}
