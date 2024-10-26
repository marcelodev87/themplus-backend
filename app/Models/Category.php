<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasUuid, Notifiable;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'enterprise_id',
    ];
}
