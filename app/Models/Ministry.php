<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Ministry extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'ministries';

    protected $fillable = [
        'name',
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
