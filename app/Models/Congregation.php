<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Congregation extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'congregations';

    protected $fillable = [
        'name',
        'date_foundation',
        'cnpj',
        'email',
        'phone',
        'cep',
        'uf',
        'address',
        'address_number',
        'neighborhood',
        'city',
        'complement',
        'member_id',
        'enterprise_id',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
