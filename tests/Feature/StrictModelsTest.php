<?php

declare(strict_types=1);

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Eloquent\MassAssignmentException;

/*
 * @note One of the three effects of the strict-model call is asserted here, and the other two
 * are taken on the framework's word. What this repository decides is which environments, and
 * that is what the two cases below pin. The best-known effect, the prohibition on lazy
 * loading, is asserted by nothing: doing so would mean adding a relationship to the
 * foundation's single model purely so that a test could violate it.
 */

it('rejects an attribute the model does not declare outside production', function (): void {
    expect(fn (): User => new User(['nickname' => 'Ada']))
        ->toThrow(MassAssignmentException::class);
});

it('discards the same attribute silently once the application runs in production', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    new AppServiceProvider(app())->boot();

    $user = new User(['nickname' => 'Ada']);

    expect($user->getAttributes())->toBe([]);
});
