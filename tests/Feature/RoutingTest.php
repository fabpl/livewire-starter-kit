<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('declares no route action as a closure', function (): void {
    $vendor = base_path('vendor').DIRECTORY_SEPARATOR;

    $closures = [];

    foreach (Route::getRoutes()->getRoutes() as $route) {
        $action = $route->getAction('uses');

        if (! $action instanceof Closure) {
            continue;
        }

        /* @note Six of the registered routes are closures the framework and Livewire declare,
           and none of them can be rewritten here. Scoping by the tree rather than by the route
           file is what makes this hold for a route file that does not exist yet. */
        $file = new ReflectionFunction($action)->getFileName();

        if (is_string($file) && str_starts_with($file, $vendor)) {
            continue;
        }

        $closures[] = $route->uri();
    }

    expect($closures)->toBe([]);
});
