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

    public static function isFormActive($enterpriseId)
    {
        $config = PreRegistrationConfig::where('enterprise_id', $enterpriseId)->first();

        if($config->active !== 1){
            throw new \Exception('O formulário para ingresso como membro está desativado');
        }
    }
}
