<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class FinancialMovement extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'financial_movements';

    protected $fillable = [
        'date_delivery',
        'month',
        'year',
        'enterprise_id',
        'check_counter',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }
}
