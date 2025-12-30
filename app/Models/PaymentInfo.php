<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PaymentInfo extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'payment_info';

    protected $fillable = [
        'payment_id',
        'user_id',
        'subscription_id',
        'month_quantity',
    ];
}
