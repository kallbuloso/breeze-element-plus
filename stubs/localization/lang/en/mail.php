<?php

return [
    'common' => [
        'greeting' => 'Hello, :name!',
        'expiration' => '{1} This link expires in :count minute.|[2,*] This link expires in :count minutes.',
        'fallback' => 'If the “:action” button does not work, copy and paste the address below into your browser:',
        'rights' => 'All rights reserved.',
    ],
    'verification' => [
        'subject' => 'Confirm your email address',
        'preview' => 'Confirm your email to start using :app.',
        'heading' => 'Confirm your email',
        'intro' => 'Thanks for creating your :app account. Confirm your email address to complete your registration.',
        'action' => 'Confirm email',
        'outro' => 'If you did not create this account, you can safely ignore this email.',
    ],
    'password_reset' => [
        'subject' => 'Reset your password',
        'preview' => 'We received a request to reset your password on :app.',
        'heading' => 'Reset your password',
        'intro' => 'We received a request to reset the password for your :app account.',
        'action' => 'Reset password',
        'outro' => 'If you did not request a new password, you can safely ignore this email.',
    ],
];
