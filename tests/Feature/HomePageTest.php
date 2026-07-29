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

/*
 * @note The Chrome the Page composes, asserted in order rather than by presence. The kit's name
 * appears twice in this response — once in the bar and once inside the installation command — so
 * `assertSee` alone would be green with no bar at all. Ordering it against the headline is what
 * makes the assertion about the bar: only a bar puts the name above the hero.
 *
 * The licence is the footer's own fact and needs no such care. Both are content the page would be
 * wrong without: what the reader has inherited, and under what terms.
 */
it('names the kit above the hero and carries its licence below', function (): void {
    get('/')
        ->assertSeeInOrder(['livewire-starter-kit', 'cannot be quietly weakened'])
        ->assertSee('Released under the MIT licence.');
});

/*
 * @note Three states and not two, which is the load-bearing decision of this control rather than
 * a detail of it: a two-state control destroys the link to the operating system on the first
 * click with no way back. That the three states *work* is the browser suite's to observe — this
 * seam can only see that all three are offered, and that is the part a later edit could quietly
 * remove.
 */
it('offers the theme in three states, system among them', function (): void {
    get('/')
        ->assertSee('Light')
        ->assertSee('Dark')
        ->assertSee('System');
});
