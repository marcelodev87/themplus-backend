<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MemberHelper
{
    public static function existsMember($enterpriseId, $name, $mode, $memberID = null)
    {
        $existingMember = DB::table('members')
            ->where('enterprise_id', $enterpriseId)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingMember) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe um membro com esse nome.'],
                ]);
            }
        } else {
            if ($existingMember && $existingMember->id !== $memberID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outro membro com esse nome.'],
                ]);
            }
        }
    }

    public static function existsMemberWithCpf($enterpriseId, $cpf, $mode, $memberID = null)
    {
        $existingMember = DB::table('members')
            ->where('enterprise_id', $enterpriseId)
            ->where('cpf', $cpf)
            ->first();

        if ($mode === 'create') {
            if ($existingMember) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe um membro com esse cpf'],
                ]);
            }
        } else {
            if ($existingMember && $existingMember->id !== $memberID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outro membro com esse cpf'],
                ]);
            }
        }
    }

    public static function hasLink($enterpriseID, $memberID): void
    {
        $memberExists = DB::table('members')
            ->where('enterprise_id', $enterpriseID)
            ->where('id', $memberID)
            ->exists();

        if (! $memberExists) {
            throw ValidationException::withMessages([
                'notFound' => ['Membro não encontrado.'],
            ]);
        }

        $links = [];

        if (DB::table('ministries')->where('member_id', $memberID)->exists()) {
            $links[] = 'ministério';
        }

        if (DB::table('networks')->where('member_id', $memberID)->exists()) {
            $links[] = 'rede';
        }

        if (
            DB::table('cells')->where(function ($query) use ($memberID) {
                $query->where('leader_id', $memberID)
                    ->orWhere('host_id', $memberID);
            })->exists()
        ) {
            $links[] = 'célula';
        }

        if (! empty($links)) {
            $message = 'Membro possui vínculo em ';

            $total = count($links);

            foreach ($links as $index => $link) {
                if ($index > 0) {
                    $message .= ($index === $total - 1) ? ' e ' : ', ';
                }

                $message .= $link;
            }

            $message .= ' e não pode ser removido.';

            throw ValidationException::withMessages([
                'linked' => [$message],
            ]);
        }
    }
}
