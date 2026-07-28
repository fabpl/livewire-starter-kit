<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

it('leaves the framework default in place outside production', function (): void {
    $validator = Validator::make(
        ['password' => 'password'],
        ['password' => Password::default()],
    );

    expect($validator->passes())->toBeTrue();
});

it('demands a long password once the application runs in production', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    new AppServiceProvider(app())->boot();

    $validator = Validator::make(
        ['password' => 'password'],
        ['password' => Password::default()],
    );

    expect($validator->passes())->toBeFalse()
        ->and($validator->errors()->first('password'))->toContain('12 characters');
});
