<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory, HasUuid, Notifiable;

    protected $table = 'accounts';

    protected $fillable = [
        'name',
        'balance',
        'enterprise_id',
    ];
}
