<?php

declare(strict_types = 1);

use App\Models\User;
use Illuminate\Support\Facades\URL;

it('redirects verified users away from the verification notice', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('verification.notice'))
        ->assertRedirect(route('dashboard', absolute: false));
});

it('does not resend verification when email is already verified', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('verification.send'))
        ->assertRedirect(route('dashboard', absolute: false));
});

it('sends verification link for unverified users', function (): void {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->post(route('verification.send'))
        ->assertRedirect()
        ->assertSessionHas('status', 'verification-link-sent');
});

it('redirects verified users hitting the signed verification url again', function (): void {
    $user = User::factory()->create();

    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $this->actingAs($user)->get($url)
        ->assertRedirect(route('dashboard', absolute: false) . '?verified=1');
});
