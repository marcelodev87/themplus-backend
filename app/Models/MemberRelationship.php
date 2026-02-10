<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MemberRelationship extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'member_relationship';

    protected $fillable = [
        'member_id',
        'related_member_id',
        'relationship_id',
    ];
}
