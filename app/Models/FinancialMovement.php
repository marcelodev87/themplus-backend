<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class FinancialMovement extends Model
{
    use HasFactory, HasUuid, Notifiable;

    protected $table = 'financial_movements';

    protected $fillable = [
        'date_delivery',
        'month',
        'year',
        'enterprise_id',
    ];
}
