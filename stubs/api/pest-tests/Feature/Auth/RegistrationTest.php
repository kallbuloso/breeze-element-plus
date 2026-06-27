<?php

use App\Models\User;

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertNoContent();
    expect(User::where('email', 'test@example.com')->value('password'))
        ->toStartWith('$peppered$'.config('hashing.pepper.id').'$');
});
