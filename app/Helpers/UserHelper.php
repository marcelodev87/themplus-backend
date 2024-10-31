<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Repositories\UserRepository;
use App\Models\User;

class UserHelper
{
    public static function validUser($email, $password)
    {
        $userRepository = new UserRepository(new User());
        $user = $userRepository->findByEmail($email);
        if (! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['A senha informada está incorreta'],
            ]);
        }
    }

}
