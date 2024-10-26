<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Subscription extends Model
{
    use HasFactory, Notifiable, HasUuid;

    protected $table = 'subscriptions';

    protected $fillable = [
        'name',
        'price',
    ];

}
