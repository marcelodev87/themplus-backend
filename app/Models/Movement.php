<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Movement extends Model
{
    use HasFactory, Notifiable, HasUuid;

    protected $table = 'movements';

    protected $fillable = [
        'type',
        'value',
        'date_movement',
        'description',
        'receipt',
        'category_id',
        'account_id',
        'enterprise_id'
    ];
}
