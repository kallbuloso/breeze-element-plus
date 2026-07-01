<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Http\Response;

class MailPreviewController extends Controller
{
    /**
     * Render an authentication email without sending it.
     */
    public function __invoke(string $type): Response
    {
        abort_unless(app()->isLocal(), 404);

        $user = new User([
            'name' => 'Alex Morgan',
            'email' => 'alex@example.com',
        ]);
        $user->id = 1;

        $notification = match ($type) {
            'verification' => new VerifyEmail,
            'reset-password' => new ResetPassword('preview-token'),
        };
        $message = $notification->toMail($user);
        $view = is_array($message->view) ? $message->view['html'] : $message->view;

        return response()->view($view, $message->data());
    }
}
