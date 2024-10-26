<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory, Notifiable, HasUuid;

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'parent_id',
        'enterprise_id'
    ];
}
