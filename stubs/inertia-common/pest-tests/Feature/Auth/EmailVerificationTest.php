<?php

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertStatus(200);
});

test('custom verification email is rendered', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create(['name' => '<Alex>']);
    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification) use ($user) {
        $message = $notification->toMail($user);
        $html = app('mailer')->render($message->view['html'], $message->data());
        $text = app('mailer')->render($message->view['text'], $message->data());

        expect($message->subject)->toBe(__('mail.verification.subject'))
            ->and($html)->toContain(__('mail.verification.action'))
            ->and($html)->toContain('&lt;Alex&gt;')
            ->and($message->viewData['actionUrl'])->toContain('/verify-email/'.$user->getKey().'/')
            ->and($text)->toContain(__('mail.verification.heading'))
            ->and($text)->toContain($message->viewData['actionUrl']);

        return true;
    });
});

test('mail previews are only available locally', function () {
    $this->get(route('mail.preview', 'verification'))->assertNotFound();

    $this->app['env'] = 'local';

    $this->get(route('mail.preview', 'verification'))
        ->assertOk()
        ->assertSee(__('mail.verification.heading'));
    $this->get(route('mail.preview', 'reset-password'))
        ->assertOk()
        ->assertSee(__('mail.password_reset.heading'));
});

test('email can be verified', function () {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
});

test('email is not verified with invalid hash', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
