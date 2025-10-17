<?php

namespace App\Helpers;

use App\Models\Enterprise;
use App\Repositories\EnterpriseRepository;
use Illuminate\Support\Facades\DB;

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
                $enterprise->email !== null;

            return $fieldsFilled;
        }

        return false;
    }

    public static function existsEnterpriseCpfOrCnpj($request)
    {
        $enterpriseRepository = new EnterpriseRepository(new Enterprise);

        if ($request->input('cpf') !== null) {
            $enterprise = $enterpriseRepository->findByCpf($request->input('cpf'));
            if ($enterprise && $enterprise->id !== $request->user()->enterprise_id) {
                throw new \Exception('O CPF já está em uso por outra conta');
            }
        }
        if ($request->input('cnpj') !== null) {
            $enterprise = $enterpriseRepository->findByCnpj($request->input('cnpj'));
            if ($enterprise && $enterprise->id !== $request->user()->enterprise_id) {
                throw new \Exception('O CNPJ já está em uso por outra conta');
            }
        }
    }

    public static function allowHeadquarters($enterpriseID)
    {
        $enterprise = DB::table('enterprises')->where('id', $enterpriseID)->first();

        if ($enterprise->created_by !== null) {
            throw new \Exception('Sua organização não é uma filial');
        }
    }
}
