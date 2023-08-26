<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;

class ResetPasswordNotification extends BaseResetPassword
{
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $user)
    {
        parent::__construct($token);
        $this->user = $user;
    }

    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('【重要】パスワードのリセットについて') // Text subject reset password
            ->markdown('mail.resetPassword', [
                'user' => $this->user,
                'resetPasswordUrl' => $url,
            ]);
    }
}
