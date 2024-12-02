<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Feedback extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'enterprise_id',
        'message',
    ];
}
