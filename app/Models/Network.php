<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Network extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'networks';

    protected $fillable = [
        'name',
        'member_id',
        'congregation_id',
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

    public function congregation()
    {
        return $this->belongsTo(Enterprise::class, 'congregation_id');
    }
}
