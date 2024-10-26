<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory, HasUuid, Notifiable;

    protected $table = 'subscriptions';

    protected $fillable = [
        'name',
        'price',
    ];
}
