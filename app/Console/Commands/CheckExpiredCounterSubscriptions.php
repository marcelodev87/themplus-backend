<?php

namespace App\Console\Commands;

use App\Services\CounterLicenseService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckExpiredCounterSubscriptions extends Command
{
    protected $signature = 'counter-subscriptions:check-expired';

    protected $description = 'Verifica assinaturas de contador (C5/C10/CIlimited) vencidas e revoga as licenças Básicas concedidas por eles';

    public function handle(CounterLicenseService $counterLicenseService)
    {
        $timezone = 'America/Sao_Paulo';
        $now = Carbon::now($timezone);
        $sevenDaysLater = $now->copy()->addDays(7)->toDateString();

        $notificationRepo = app(\App\Repositories\NotificationRepository::class);

        /*
        | 1️⃣ Aviso: faltam 7 dias
        */
        DB::table('enterprises')
            ->join('subscriptions', 'subscriptions.id', '=', 'enterprises.subscription_id')
            ->where('subscriptions.type', 'counter')
            ->whereNotNull('enterprises.expired_date')
            ->whereDate('enterprises.expired_date', '=', $sevenDaysLater)
            ->select('enterprises.id')
            ->orderBy('enterprises.id')
            ->chunk(100, function ($enterprises) use ($notificationRepo) {
                foreach ($enterprises as $enterprise) {
                    $notificationRepo->create(
                        $enterprise->id,
                        'Sua assinatura de contador está perto de vencer!',
                        'Faltam apenas 7 dias para o vencimento do seu plano. Renove para não perder o acesso e para que seus clientes não percam a assinatura Básica concedida.'
                    );
                }
            });

        /*
        | 2️⃣ Processar expiração: revoga todas as licenças concedidas e volta o contador para "sem plano"
        */
        $expiredCounterIds = DB::table('enterprises')
            ->join('subscriptions', 'subscriptions.id', '=', 'enterprises.subscription_id')
            ->where('subscriptions.type', 'counter')
            ->whereNotNull('enterprises.expired_date')
            ->where('enterprises.expired_date', '<=', $now->toDateTimeString())
            ->pluck('enterprises.id');

        $totalCounters = 0;
        $totalLicensesRevoked = 0;

        foreach ($expiredCounterIds as $counterEnterpriseId) {
            $revoked = $counterLicenseService->revokeAllByCounter($counterEnterpriseId);
            $totalLicensesRevoked += count($revoked);
            $totalCounters++;

            DB::table('enterprises')->where('id', $counterEnterpriseId)->update([
                'subscription_id' => null,
                'expired_date' => null,
                'updated_at' => $now,
            ]);

            $notificationRepo->create(
                $counterEnterpriseId,
                'Assinatura de contador expirada',
                'Sua assinatura expirou e todas as assinaturas Básicas concedidas aos seus clientes foram removidas (voltaram para o plano gratuito).'
            );
        }

        $this->info("Processamento concluído. Contadores expirados: {$totalCounters}. Licenças revogadas: {$totalLicensesRevoked}.");
    }
}
