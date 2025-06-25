<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class FeedbackSaved extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'feedbacks_saved';

    protected $fillable = [
        'user_name',
        'user_email',
        'enterprise_name',
        'date_feedback',
        'message',
    ];
}
