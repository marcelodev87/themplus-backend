<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecalculateAccountBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = DB::table('accounts')->get();

        $this->command->info("Total de contas encontradas: {$accounts->count()}");
        $this->command->newLine();

        $bar = $this->command->getOutput()->createProgressBar($accounts->count());
        $bar->start();

        foreach ($accounts as $account) {
            $movements = DB::table('movements')
                ->where('account_id', $account->id)
                ->get();

            $balance = 0;

            foreach ($movements as $movement) {
                if ($movement->type === 'entrada') {
                    $balance += $movement->value;
                } elseif ($movement->type === 'saída') {
                    $balance -= $movement->value;
                } else {
                    $category = DB::table('categories')->find($movement->category_id);

                    if ($category) {
                        if ($category->type === 'entrada') {
                            $balance += $movement->value;
                        } else {
                            $balance -= $movement->value;
                        }
                    }
                }
            }

            DB::table('accounts')
                ->where('id', $account->id)
                ->update(['balance' => $balance]);

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info('✅ Saldos recalculados com sucesso!');
    }
}
