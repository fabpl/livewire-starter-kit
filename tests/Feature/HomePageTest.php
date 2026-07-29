<?php

declare(strict_types=1);

use function Pest\Laravel\get;

/*
 * @note This absorbs the example test the kit shipped with, which asserted the same response
 * without naming what it was verifying. The seam is deliberately the outermost one: the Page
 * carries no `render` method, so its view is found by configuration rather than by code, and a
 * request through the route is the only place that resolution is observable.
 */

it('serves the home page at the root route', function (): void {
    get('/')->assertOk()->assertSee('Livewire Starter Kit');
});
