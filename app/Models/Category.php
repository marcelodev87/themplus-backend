<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Category extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'type',
        'enterprise_id',
        'alert',
        'active',
        'default',
    ];

    public function alert()
    {
        return $this->belongsTo(Alert::class);
    }
}
