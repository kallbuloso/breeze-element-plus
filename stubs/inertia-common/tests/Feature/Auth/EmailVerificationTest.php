<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_custom_verification_email_is_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create(['name' => '<Alex>']);
        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification) use ($user) {
            $message = $notification->toMail($user);
            $html = app('mailer')->render($message->view['html'], $message->data());
            $text = app('mailer')->render($message->view['text'], $message->data());

            $this->assertSame(__('mail.verification.subject'), $message->subject);
            $this->assertStringContainsString(__('mail.verification.action'), $html);
            $this->assertStringContainsString('&lt;Alex&gt;', $html);
            $this->assertStringContainsString('/verify-email/'.$user->getKey().'/', $message->viewData['actionUrl']);
            $this->assertStringContainsString(__('mail.verification.heading'), $text);
            $this->assertStringContainsString($message->viewData['actionUrl'], $text);

            return true;
        });
    }

    public function test_mail_previews_are_only_available_locally(): void
    {
        $this->get(route('mail.preview', 'verification'))->assertNotFound();

        $this->app['env'] = 'local';

        $this->get(route('mail.preview', 'verification'))
            ->assertOk()
            ->assertSee(__('mail.verification.heading'));
        $this->get(route('mail.preview', 'reset-password'))
            ->assertOk()
            ->assertSee(__('mail.password_reset.heading'));
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
