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
        'enterprise_id',
        'enterprise_counter_id',
        'description',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function counter()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_counter_id');
    }
}
