<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Member extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'members';

    protected $fillable = [
        'name',
        'profession',
        'date_birth',
        'naturalness',
        'marital_status',
        'education',
        'cpf',
        'email',
        'email_professional',
        'phone',
        'phone_professional',
        'cep',
        'uf',
        'address',
        'address_number',
        'neighborhood',
        'city',
        'complement',
        'type',
        'active',
        'date_baptismo',
        'start_date',
        'reason_start_date',
        'church_start_date',
        'end_date',
        'reason_end_date',
        'church_end_date',
        'enterprise_id',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'member_role', 'member_id', 'role_id');
    }
}
