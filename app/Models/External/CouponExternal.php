<?php

namespace App\Models\External;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CouponExternal extends Model
{
    use HasUuid, Notifiable;

    protected $connection = 'external';

    protected $table = 'coupons';

    protected $fillable = [
        'name',
        'movements',
        'allow_financial',
        'allow_members',
        'allow_assistant_whatsapp',
        'discount',
        'date_expires',
    ];
}
