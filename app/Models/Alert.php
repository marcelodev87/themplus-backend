<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Alert extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'alerts';

    protected $fillable = [
        'description',
        'enterprise_id',
    ];
}
