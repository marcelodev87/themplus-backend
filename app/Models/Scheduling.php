<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scheduling extends Model
{
    use HasFactory, Notifiable, HasUuid;

    protected $table = 'schedulings';

    protected $fillable = [
        'date_movement',
        'enterprise_id',
    ];
}
