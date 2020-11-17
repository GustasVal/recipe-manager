<?php

namespace App\Notifications\Users;

use App\Models\Users\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;

class VerifyEmail extends Notification
{
    protected $baseEmail;

    public function __construct()
    {
        $this->baseEmail = new BaseVerifyEmail();
    }

    public function setBaseEmail(BaseVerifyEmail $baseEmail): void
    {
        $this->baseEmail = $baseEmail;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail(User $notifiable)
    {
        if ($notifiable->exists) {
            return $this->baseEmail->toMail($notifiable);
        }

        $password = $notifiable->generatePassword();
        $notifiable->password = Hash::make($password);
        $notifiable->save();
        $notifiable->markEmailAsVerified();

        $loginUrl = URL::route('login');

        // This is mainly a phpstan fix
        $subject = !is_array(Lang::get('Account created')) ? Lang::get('Account created') : 'Account created';
        $action = !is_array(Lang::get('Login')) ? Lang::get('Login') : 'Login';

        return (new MailMessage)
            ->subject($subject)
            ->line(Lang::get('A new account was created for you.'))
            ->line(Lang::get('Use the following password to login: :password', ['password' => $password]))
            ->action($action, $loginUrl)
            ->line(Lang::get('You should change your password after the first login.'));
    }
}
