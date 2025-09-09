<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CellMember extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'cell_members';

    protected $fillable = [
        'member_id',
        'cell_id',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function congregation()
    {
        return $this->belongsTo(Congregation::class, 'congregation_id');
    }
}
