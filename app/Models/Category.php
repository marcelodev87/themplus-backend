<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, Notifiable, HasUuid;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'enterprise_id'
    ];
}
