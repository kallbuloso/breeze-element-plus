<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'is_owner',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_owner' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new ResetPassword($token));
    }
}
