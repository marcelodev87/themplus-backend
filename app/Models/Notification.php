<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Notification extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'notifications';

    protected $fillable = [
        'enterprise_id',
        'user_id',
        'read',
        'title',
        'text',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
