<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteOldRegisters extends Command
{
    protected $signature = 'registers:clean-old';

    protected $description = 'Exclui registros da tabela register com created_at mais antigo que 2 meses';

    public function handle()
    {
        $limitDate = Carbon::now()->subMonths(2);

        $deleted = DB::table('registers')
            ->where('created_at', '<', $limitDate)
            ->delete();

        $this->info("Registros excluídos: {$deleted}");

        return Command::SUCCESS;
    }
}
