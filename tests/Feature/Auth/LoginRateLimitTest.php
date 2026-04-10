<?php

declare(strict_types = 1);

use App\Models\User;

it('throttles login after too many failed attempts', function (): void {
    $user = User::factory()->create();

    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});
