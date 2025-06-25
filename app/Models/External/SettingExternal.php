<?php

namespace App\Models\External;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingExternal extends Model
{
    use HasFactory;

    protected $connection = 'external';

    protected $table = 'settings';

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'valeu',
    ];
}
