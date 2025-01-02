<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'user_counter_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function counter()
    {
        return $this->belongsTo(User::class, 'user_counter_id');
    }
}
