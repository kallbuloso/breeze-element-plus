<?php

use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
        $message = $notification->toMail($user);
        $html = app('mailer')->render($message->view['html'], $message->data());
        $text = app('mailer')->render($message->view['text'], $message->data());

        expect($message->subject)->toBe(__('mail.password_reset.subject'))
            ->and($html)->toContain(__('mail.password_reset.action'))
            ->and($message->viewData['actionUrl'])->toStartWith(config('app.frontend_url').'/password-reset/')
            ->and($text)->toContain($message->viewData['actionUrl']);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function (object $notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertStatus(200);

        return true;
    });
});
