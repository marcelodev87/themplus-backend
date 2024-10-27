<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Enterprise extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'enterprises';

    protected $fillable = [
        'name',
        'cnpj',
        'cpf',
        'country',
        'state',
        'city',
        'address',
        'email',
        'phone',
        'subscription_id',
    ];
}
