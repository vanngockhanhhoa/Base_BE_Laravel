<?php

namespace App\Notifications;

use App\Models\Account;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;

class NewPasswordNotification extends BaseResetPassword
{

    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void'
     */
    public function __construct($token, $user)
    {
        parent::__construct($token);
        $this->user = $user;
    }

    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('{カクテン屋 } アカウント発行のお知らせ') // Text subject create account success
            ->markdown('mail.newPassword', [
                'user' => $this->user,
                'newPasswordUrl' => $url,
                'loginUrl' => env('FRONTEND_URL') . '/login'
            ]);
    }
}
