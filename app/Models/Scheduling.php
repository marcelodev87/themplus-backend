<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Scheduling extends Model
{
    use HasFactory, HasUuid, Notifiable;

    protected $table = 'schedulings';

    protected $fillable = [
        'date_movement',
        'enterprise_id',
    ];
}
