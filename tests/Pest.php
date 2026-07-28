<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature', 'Unit', 'Browser');

/*
 * @note These two belong to the suite, so the suite installs them and the application service
 * provider is deliberately left out of it. A provider would have to ask whether it is running
 * under test, and that question is always answered yes while the suite runs: a mutant removing
 * the condition could not be killed by any test, which would put an unprovable branch inside
 * the measured perimeter. Installed here the condition disappears — this file is reachable only
 * under test, so there is no guard to write and nothing to prove about one.
 *
 * The browser suite is left out for its own reasons: it is already outside the coverage
 * measurement, and it has its own notion of waiting and its own network.
 *
 * Each of the two is asserted from a different suite — the prohibition from `Feature`, the
 * faked sleep from `Unit` — so that dropping either name from the scope below fails a test
 * rather than passing in silence.
 *
 * Their asymmetry is worth knowing before reaching for either. The prohibition raises, so an
 * unfaked call cannot pass unnoticed; the faked sleep substitutes silently, so a test meaning
 * to verify a delay passes vacuously unless it asserts on the recorded sleeps.
 */
pest()->beforeEach(function (): void {
    Http::preventStrayRequests();

    Sleep::fake();
})->in('Feature', 'Unit');
