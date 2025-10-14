<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MemberRole extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'member_role';

    protected $fillable = [
        'member_id',
        'role_id',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
