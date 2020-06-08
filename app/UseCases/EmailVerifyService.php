<?php

namespace App\UseCases;

use App\Models\EmailVerify;
use Carbon\Carbon;

class EmailVerifyService
{
    public function sendVerifyMail(string $email, string $userGuid)
    {
        $code = rand(0000, 9999);

        EmailVerify::create([
            'user_guid' => $userGuid,
            'code' => $code,
            'email' => $email
        ]);

        if(!EmailVerify::where(['email' => $email, 'verify_at' => null])->first())
            mail($email, 'mkksentimo.ru: Подтверждение электронной почты', "Ваш код активации: $code");
    }

    public function isCorrectVerify(string $userGuid, string $code)
    {
        $row = EmailVerify::where([
            'user_guid' => $userGuid,
            'code' => $code
        ])->first();

        if($row) {
            $row->verify_at = Carbon::now();
            $row->save();
            return true;
        }
        else return false;
    }
}
