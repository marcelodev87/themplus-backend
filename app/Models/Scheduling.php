<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Scheduling extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'schedulings';

    protected $fillable = [
        'date_movement',
        'enterprise_id',
        'type',
        'value',
        'description',
        'receipt',
        'category_id',
        'account_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
