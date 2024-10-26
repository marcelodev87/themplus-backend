<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory, HasUuid, Notifiable;

    protected $table = 'alerts';

    protected $fillable = [
        'description',
        'enterprise_id',
    ];
}
