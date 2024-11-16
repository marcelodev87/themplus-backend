<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Rap2hpoutre\FastExcel\FastExcel;

class UserExport
{
    protected $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    public function download($fileName)
    {
        return (new FastExcel($this->users))->download($fileName, function ($user) {
            return [
                'ID' => $user->id,
                'NAME' => $user->name,
                'EMAIL' => $user->email,
                'TELEFONE' => $user->phone,
                'CARGO' => $user->position === 'admin' ? 'Administrador' : 'Usuário comum',
                'DEPARTAMENTO' => $user->department_id ? $user->department->name : '',
                'CRIADO EM' => $user->created_at->format('Y-m-d H:i:s'),
                'ATUALIZADO EM' => $user->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}
