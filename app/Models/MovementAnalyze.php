<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MovementAnalyze extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'movements_analyze';

    protected $fillable = [
        'type',
        'value',
        'description',
        'enterprise_id',
        'date_movement',
        'receipt'
    ];
}
