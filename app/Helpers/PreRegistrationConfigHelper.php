<?php

namespace App\Helpers;

use App\Models\PreRegistrationConfig;

class PreRegistrationConfigHelper
{
    public static function createDefault($enterpriseId)
    {
        PreRegistrationConfig::create([
            'enterprise_id' => $enterpriseId,
            'active' => 0,
        ]);
    }
}
