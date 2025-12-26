<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PreRegistrationRelationship extends Model
{
    use Notifiable;

    protected $table = 'pre_registration_relationship';

    public $incrementing = false;

    protected $primaryKey = null;

    protected $keyType = 'string';

    protected $fillable = [
        'pre_registration_id',
        'member',
        'kinship',
    ];

    public function preRegistration()
    {
        return $this->belongsTo(PreRegistration::class, 'pre_registration_id');
    }
}
