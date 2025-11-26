<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Relationship extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'relationships';

    protected $fillable = [
        'name',
        'enterprise_id',
        'default',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
