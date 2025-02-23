<?php

namespace App\Observers;

use App\Models\Enterprise;
use Illuminate\Support\Facades\DB;

class EnterpriseObserver
{
    /**
     * Handle the Enterprise "created" event.
     */
    public function created(Enterprise $enterprise): void
    {
        //
    }

    /**
     * Handle the Enterprise "updated" event.
     */
    public function updated(Enterprise $enterprise): void
    {
        // Limpar as observações da organização ao não ter contabilidade vinculada
        if ($enterprise->counter_enterprise_id === null) {
            DB::table('movements')
                ->where('enterprise_id', $enterprise->id)
                ->whereNotNull('observation')
                ->update(['observation' => null]);
        }
    }

    /**
     * Handle the Enterprise "deleted" event.
     */
    public function deleted(Enterprise $enterprise): void
    {
        //
    }

    /**
     * Handle the Enterprise "restored" event.
     */
    public function restored(Enterprise $enterprise): void
    {
        //
    }

    /**
     * Handle the Enterprise "force deleted" event.
     */
    public function forceDeleted(Enterprise $enterprise): void
    {
        //
    }
}
