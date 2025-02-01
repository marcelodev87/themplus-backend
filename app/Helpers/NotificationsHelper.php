<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class NotificationsHelper
{
    public static function getNoRead($userId)
    {
        return DB::table('notifications')->where('user_id', $userId)
            ->where('read', 0)
            ->count();
    }
}
