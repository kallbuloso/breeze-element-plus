<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            foreach ($this->rateLimits() as $limit) {
                RateLimiter::hit($limit['key'], $limit['decaySeconds']);
            }

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Keep broader counters so a successful login cannot reset abuse from this IP or account.
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        $seconds = 0;

        foreach ($this->rateLimits() as $limit) {
            if (RateLimiter::tooManyAttempts($limit['key'], $limit['maxAttempts'])) {
                $seconds = max($seconds, RateLimiter::availableIn($limit['key']));
            }
        }

        if ($seconds === 0) {
            return;
        }

        event(new Lockout($this));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return $this->rateLimits()[0]['key'];
    }

    /**
     * @return array<int, array{key: string, maxAttempts: int, decaySeconds: int}>
     */
    protected function rateLimits(): array
    {
        $email = Str::lower(trim((string) $this->input('email')));
        $ip = (string) ($this->ip() ?? 'unknown');
        $emailKey = hash('sha256', $email);
        $ipKey = hash('sha256', $ip);

        return [
            [
                'key' => 'login:identity:'.hash('sha256', $email.'|'.$ip),
                'maxAttempts' => 5,
                'decaySeconds' => 60,
            ],
            [
                'key' => 'login:ip:'.$ipKey,
                'maxAttempts' => 20,
                'decaySeconds' => 60,
            ],
            [
                'key' => 'login:email:'.$emailKey,
                'maxAttempts' => 50,
                'decaySeconds' => 3600,
            ],
        ];
    }
}
