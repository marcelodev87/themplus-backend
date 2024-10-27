<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Subscription extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'subscriptions';

    protected $fillable = [
        'name',
        'price',
    ];
}
