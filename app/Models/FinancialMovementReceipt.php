<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class FinancialMovementReceipt extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'financial_movements_receipts';

    protected $fillable = [
        'name',
        'receipt',
        'enterprise_id',
        'financial_movements_id',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }
}
