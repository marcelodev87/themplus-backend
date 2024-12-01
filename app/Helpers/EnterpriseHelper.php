<?php

namespace App\Helpers;

use App\Models\Enterprise;
use App\Repositories\EnterpriseRepository;

class EnterpriseHelper
{
    public static function filledData($enterpriseId)
    {
        $enterpriseRepository = new EnterpriseRepository(new Enterprise);
        $enterprise = $enterpriseRepository->findById($enterpriseId);

        if ($enterprise) {
            $fieldsFilled = ($enterprise->cnpj !== null || $enterprise->cpf !== null) &&
                       $enterprise->cep !== null &&
                       $enterprise->state !== null &&
                       $enterprise->city !== null &&
                       $enterprise->neighborhood !== null &&
                       $enterprise->address !== null &&
                       $enterprise->number_address !== null &&
                       $enterprise->email !== null &&
                       $enterprise->phone !== null;

            return $fieldsFilled;
        }

        return false;
    }
}
