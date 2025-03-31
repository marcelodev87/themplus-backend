<?php

namespace App\Helpers;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserHelper
{
    public static function validUser($email, $password)
    {
        $userRepository = new UserRepository(new User);
        $user = $userRepository->findByEmail($email);
        if (! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['A senha informada está incorreta'],
            ]);
        }
    }

    public static function clearTokenReset($user)
    {
        DB::table('password_resets')->where('email', $user->email)->delete();
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();
    }
}
