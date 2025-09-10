<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MemberMinistry extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'ministry_members';

    protected $fillable = [
        'member_id',
        'ministry_id',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id');
    }
}
