<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    use App\Traits\HasUuid;
    use HasFactory, HasUuid, Notifiable;

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
