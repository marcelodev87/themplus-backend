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

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'type',
        'service',
        'subscription_id',
        'discount',
        'date_expiration',
    ];
}
