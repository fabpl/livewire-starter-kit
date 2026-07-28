<?php

declare(strict_types=1);

use App\Models\User;

it('reduces a multi-word name to its outermost initials', function (): void {
    $user = new User(['name' => 'Ada Byron Lovelace']);

    expect($user->initials())->toBe('AL');
});

it('keeps the single initial of a one-word name', function (): void {
    $user = new User(['name' => 'Ada']);

    expect($user->initials())->toBe('A');
});
