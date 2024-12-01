<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Account extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'accounts';

    protected $fillable = [
        'name',
        'balance',
        'enterprise_id',
        'agency_number',
        'account_number',
        'description',
        'active',
    ];
}
