<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckExpiredEnterprises extends Command
{
    protected $signature = 'enterprises:check-expired';

    protected $description = 'Downgrade enterprises with expired subscription';

    public function handle()
    {
        $now = Carbon::now();

        $freeId = DB::table('subscriptions')
            ->where('name', 'free')
            ->value('id');

        if (! $freeId) {
            $this->error('Plano free não encontrado');

            return;
        }

        $updated = DB::table('enterprises')
            ->whereNotNull('expired_date')
            ->where('expired_date', '<=', $now)
            ->update([
                'expired_date' => null,
                'subscription_id' => $freeId,
                'updated_at' => $now,
            ]);

        $this->info("Empresas atualizadas: {$updated}");
    }
}
