<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PreRegistrationConfig extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'pre_registration_config';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'enterprise_id',
        'active',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }
}
