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
        $timezone = 'America/Sao_Paulo';
        $now = Carbon::now($timezone);
        $sevenDaysLater = $now->copy()->addDays(7)->toDateString();

        $freePlan = DB::table('subscriptions')->where('name', 'free')->first();

        if (! $freePlan) {
            $this->error('Plano free não encontrado');

            return;
        }

        $notificationRepo = app(\App\Repositories\NotificationRepository::class);

        /*
        | 1️⃣ Aviso: faltam 7 dias
        */
        DB::table('enterprises')
            ->whereNotNull('expired_date')
            ->whereDate('expired_date', '=', $sevenDaysLater)
            ->orderBy('id')
            ->chunk(100, function ($enterprises) use ($notificationRepo) { // Corrigido aqui
                foreach ($enterprises as $enterprise) {
                    $notificationRepo->create(
                        $enterprise->id,
                        'Sua assinatura está perto de vencer!',
                        'Faltam apenas 7 dias para o vencimento do seu plano. Recomendamos acessar a Central de Assinaturas e renovar seu pacote com antecedência para não interromper seus recursos.'
                    );
                }
            });

        /*
        | 2️⃣ Processar Expiração (Notificar e Atualizar usando chunk para segurança)
        */
        $queryExpire = DB::table('enterprises')
            ->whereNotNull('expired_date')
            ->where('expired_date', '<=', $now->toDateTimeString());

        // Total para o log final
        $totalToUpdate = $queryExpire->count();

        $queryExpire->orderBy('id')->chunk(100, function ($enterprises) use ($notificationRepo) {
            foreach ($enterprises as $enterprise) {
                $notificationRepo->create(
                    $enterprise->id,
                    'Assinatura expirada',
                    'Sua assinatura expirou. Seu acesso foi alterado para o plano gratuito.'
                );
            }
        });

        // Update em massa após as notificações
        $updated = DB::table('enterprises')
            ->whereNotNull('expired_date')
            ->where('expired_date', '<=', $now->toDateTimeString())
            ->update([
                'subscription_id' => $freePlan->id,
                'expired_date' => null,
                'allow_test_subscription' => 0,
                'updated_at' => $now,
            ]);

        $this->info("Processamento concluído. Empresas notificadas: {$totalToUpdate}. Atualizadas para o Free: {$updated}");
    }
}
