<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PasswordReset extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'password_resets';

    protected $fillable = [
        'email',
        'code',
    ];
}
