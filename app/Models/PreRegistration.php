<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PreRegistration extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'pre_registrations';

    protected $fillable = [
        'enterprise_id',
        'name',
        'email',
        'phone',
        'role',
        'description',
        'profession',
        'date_birth',
        'naturalness',
        'marital_status',
        'education',
        'cpf',
        'cep',
        'uf',
        'address',
        'address_number',
        'neighborhood',
        'city',
        'complement',
        'date_baptismo',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function relationships()
    {
        return $this->hasMany(PreRegistrationRelationship::class, 'pre_registration_id');
    }
}
