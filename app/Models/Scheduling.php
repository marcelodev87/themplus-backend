<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Scheduling extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'schedulings';

    protected $fillable = [
        'date_movement',
        'enterprise_id',
    ];
}
