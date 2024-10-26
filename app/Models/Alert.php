<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Alert extends Model
{
    use HasFactory, Notifiable, HasUuid;

    protected $table = 'alerts';

    protected $fillable = [
        'description',
        'enterprise_id'
    ];
}
