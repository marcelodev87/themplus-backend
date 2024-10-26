<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class FinancialMovement extends Model
{
    use HasFactory, Notifiable, HasUuid;

    protected $table = 'financial_movements';

    protected $fillable = [
        'date_delivery',
        'month',
        'year',
        'enterprise_id',
    ];
}
