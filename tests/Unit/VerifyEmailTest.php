<?php

namespace Tests\Feature;

use App\Models\Users\User;
use App\Notifications\Users\VerifyEmail;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    private $verifyEmail;

    private $baseEmail;

    public function setUp(): void
    {
        $this->verifyEmail = new VerifyEmail();
        $this->baseEmail = $this->createMock(BaseVerifyEmail::class);

        $this->verifyEmail->setBaseEmail($this->baseEmail);
    }

    /** @test */
    public function notifiable_does_not_exists()
    {
        $notifiable = $this->createMock(User::class);
        $notifiable->exists = false;

        $notifiable->expects(self::exactly(1))->method('generatePassword')->willReturn('123123123');

        Hash::shouldReceive('make')
            ->once()
            ->with('123123123')
            ->andReturn('hash123123123')
        ;

        $notifiable->expects(self::exactly(1))->method('save')->willReturn(true);
        $notifiable->expects(self::exactly(1))->method('markEmailAsVerified')->willReturn(true);

        URL::shouldReceive('route')
            ->once()
            ->with('login')
            ->andReturn('127.0.0.1/login')
        ;

        Lang::shouldReceive('get')
            ->twice()
            ->with('Account created')
            ->andReturn('Account created')
        ;

        Lang::shouldReceive('get')
            ->twice()
            ->with('Login')
            ->andReturn('Login')
        ;

        Lang::shouldReceive('get')
            ->once()
            ->with('A new account was created for you.')
            ->andReturn('A new account was created for you.')
        ;

        Lang::shouldReceive('get')
            ->once()
            ->with('Use the following password to login: :password', ['password' => '123123123'])
            ->andReturn('Use the following password to login: 123123123')
        ;

        Lang::shouldReceive('get')
            ->once()
            ->with('You should change your password after the first login.')
            ->andReturn('You should change your password after the first login.')
        ;

        $result = $this->verifyEmail->toMail($notifiable);

        $expectedResult = (new MailMessage())
            ->subject('Account created')
            ->line('A new account was created for you.')
            ->line('Use the following password to login: 123123123')
            ->action('Login', '127.0.0.1/login')
            ->line('You should change your password after the first login.')
        ;

        $this->assertEquals($expectedResult, $result);
    }

    /** @test */
    public function notifiable_exists()
    {
        $notifiable = $this->createMock(User::class);
        $notifiable->exists = true;

        $this->baseEmail
            ->expects(self::exactly(1))
            ->method('toMail')
            ->with($notifiable)
            ->willReturn(new MailMessage())
        ;

        $result = $this->verifyEmail->toMail($notifiable);
    }
}
