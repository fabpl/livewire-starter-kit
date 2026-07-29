<?php

declare(strict_types=1);

use function Pest\Laravel\get;

/*
 * @note This absorbs the example test the kit shipped with, which asserted the same response
 * without naming what it was verifying. The seam is deliberately the outermost one: the Page
 * carries no `render` method, so its view is found by configuration rather than by code, and a
 * request through the route is the only place that resolution is observable.
 *
 * It is also where the Primitives are asserted, and never in isolation. Blaze folds a Primitive
 * into its parent, so a Primitive rendered alone is an artefact that does not exist at runtime —
 * the Page that renders it is both the highest seam available and the only honest one.
 */

it('serves the home page at the root route', function (): void {
    get('/')->assertOk();
});

/*
 * @note Load-bearing content, and nothing beyond it. Each of the three is something the page
 * would be broken without: the claim it makes about itself, the command that acts on that claim,
 * and the source a reader is invited to check it against. Wording that could be rewritten without
 * the page changing meaning is left out, so that editing the prose does not redden the suite.
 */
it('states what the kit is, and offers both a way to install it and a way to read it', function (): void {
    get('/')
        ->assertSee('cannot be quietly weakened')
        ->assertSee('laravel new my-app --using=fabpl/livewire-starter-kit')
        ->assertSee('https://github.com/fabpl/livewire-starter-kit');
});
