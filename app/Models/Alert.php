<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory, Notifiable, HasUuid;

    protected $table = 'alerts';

    protected $fillable = [
        'description',
        'enterprise_id'
    ];
}
