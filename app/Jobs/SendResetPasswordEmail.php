<?php

namespace App\Jobs;

use App\Mail\ResetPasswordMail;
use App\Models\PasswordReset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendResetPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function handle()
    {
        $code = random_int(10000000, 99999999);

        PasswordReset::updateOrCreate(
            ['email' => $this->user->email],
            ['code' => $code]
        );

        Mail::to($this->user->email)->send(new ResetPasswordMail($code, $this->user->name));
    }
}
