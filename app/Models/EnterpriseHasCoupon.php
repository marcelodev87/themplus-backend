<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EnterpriseHasCoupon extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'enterprise_has_coupons';

    protected $fillable = [
        'enterprise_id',
        'coupon_id',
    ];
}
