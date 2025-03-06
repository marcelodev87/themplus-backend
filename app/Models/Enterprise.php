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
        'cep',
        'state',
        'city',
        'neighborhood',
        'address',
        'complement',
        'email',
        'phone',
        'subscription_id',
        'number_address',
        'created_by',
        'position',
        'counter_enterprise_id',
        'code_financial'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'enterprise_id');
    }
}
