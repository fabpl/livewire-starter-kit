<?php

declare(strict_types=1);

use App\Livewire\Pages\Home;
use Illuminate\Support\Facades\Route;

/*
 * @note The macro takes the component class rather than its dot name. Both are accepted; a class
 * reference is checked by the analyser, so moving or renaming the Page breaks the analysis rather
 * than the page. The macro expands to a controller action, so `RoutingTest`'s prohibition on
 * route closures is unaffected.
 */
Route::livewire('/', Home::class)->name('home');
