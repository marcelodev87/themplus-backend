<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Movement extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'movements';

    protected $fillable = [
        'type',
        'value',
        'date_movement',
        'description',
        'receipt',
        'category_id',
        'account_id',
        'enterprise_id',
    ];
}
