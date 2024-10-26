<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasFactory, HasUuid, Notifiable;

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
