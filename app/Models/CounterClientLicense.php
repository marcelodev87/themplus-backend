<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class CounterClientLicense extends Model
{
    use HasUuid;

    protected $table = 'counter_client_licenses';

    protected $fillable = [
        'counter_enterprise_id',
        'client_enterprise_id',
        'subscription_id',
        'active',
        'granted_at',
        'revoked_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function counterEnterprise()
    {
        return $this->belongsTo(Enterprise::class, 'counter_enterprise_id');
    }

    public function clientEnterprise()
    {
        return $this->belongsTo(Enterprise::class, 'client_enterprise_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }
}
