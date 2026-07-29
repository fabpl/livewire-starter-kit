<?php

declare(strict_types=1);

it('serves the home page to a browser without writing to its console', function (): void {
    visit(route('home'))
        ->assertNoConsoleLogs()
        ->assertNoJavaScriptErrors()
        ->assertSee('Livewire Starter Kit');
});
