<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    use HasFactory, Notifiable, HasUuid;
    use App\Traits\HasUuid;

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
        'subscription_id'
    ];
}
