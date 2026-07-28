<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;

/*
 * @note The request is bound rather than the configured URL set, because the scheme both cases
 * assert comes from the request and not from the configuration. Binding it is what the
 * framework itself does for a console run, and rebinding it reaches the URL generator; setting
 * `app.url` after boot would not. It is deliberately created over plain HTTP, which is the
 * situation behind a proxy that terminates TLS: the scheme the request reports is the wrong
 * one, so a test that inherited the ambient URL could assert the right result for no reason.
 */
beforeEach(function (): void {
    app()->instance('request', Request::create('http://foundation.test'));
});

it('leaves the framework scheme in place outside production', function (): void {
    expect(url('/dashboard'))->toBe('http://foundation.test/dashboard');
});

/*
 * @note This pins an accepted cost as much as a protection, and the symptom is confusing
 * enough to be worth naming: an application deployed with the production environment but served
 * over plain HTTP generates URLs nobody can follow, because the request's own scheme stops
 * being consulted. The remedy is to comment out `URL::forceHttps()` in `AppServiceProvider`,
 * the call this case fails without.
 */
it('generates every URL over HTTPS once the application runs in production', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    new AppServiceProvider(app())->boot();

    expect(url('/dashboard'))->toBe('https://foundation.test/dashboard');
});
