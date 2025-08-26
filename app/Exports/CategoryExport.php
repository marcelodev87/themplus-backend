<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Rap2hpoutre\FastExcel\FastExcel;

class CategoryExport
{
    protected $categories;

    public function __construct(Collection $categories)
    {
        $this->categories = $categories;
    }

    public function download($fileName)
    {
        return (new FastExcel($this->categories))->download($fileName, function ($category) {
            return [
                'ID' => $category->id,
                'NOME' => $category->name,
                'TIPO' => $category->type,
                'ATIVO' => $category->active,
                'PADRÃO' => $category->default,
                'ALERTA' => $category->alert ?? '',
                'CÓDIGO DEBITO' => $category->code_debt ?? '',
                'CÓDIGO CRÉDITO' => $category->code_credit ?? '',
            ];
        });
    }
}
