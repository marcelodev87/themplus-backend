<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SettingsCounter extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'settings_counter';

    protected $fillable = [
        'enterprise_id',
        'allow_add_user',
        'allow_edit_user',
        'allow_delete_user',
        'allow_edit_movement',
        'allow_delete_movement',

    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
