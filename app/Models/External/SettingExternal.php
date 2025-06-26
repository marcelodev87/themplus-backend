<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Model;

class SettingExternal extends Model
{
    protected $connection = 'external';

    protected $table = 'settings';

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];
}
