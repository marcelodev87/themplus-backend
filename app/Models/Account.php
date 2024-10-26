<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Account extends Model
{
    use HasFactory, Notifiable, HasUuid;

    protected $table = 'accounts';

    protected $fillable = [
        'name',
        'balance',
        'enterprise_id'
    ];
}
