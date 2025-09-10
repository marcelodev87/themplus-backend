<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CellMemberRule
{
    public function create($request)
    {
        $rules = [
            'cellID' => 'required|exists:cells,id',
            'memberID' => 'required|exists:members,id',
        ];

        $messages = [
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
