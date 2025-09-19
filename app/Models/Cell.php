<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Cell extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'cells';

    protected $fillable = [
        'name',
        'date_foundation',
        'date_end',
        'leader_id',
        'host_id',
        'network_id',
        'enterprise_id',
        'active',
        'location',
        'day_week',
        'frequency',
        'time',
        'cep',
        'uf',
        'address',
        'address_number',
        'neighborhood',
        'city',
        'complement',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function leader()
    {
        return $this->belongsTo(Member::class, 'leader_id');
    }

    public function host()
    {
        return $this->belongsTo(Member::class, 'host_id');
    }

    public function network()
    {
        return $this->belongsTo(Network::class, 'network_id');
    }
}
