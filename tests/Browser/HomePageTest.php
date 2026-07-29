<?php

declare(strict_types=1);

/*
 * @note The audit joins this suite now rather than at the end of the effort. Added last, it
 * would find its defects when they are most expensive to fix; added here, against a page that
 * is still almost empty, every later ticket has to keep it green while building the content it
 * covers. That is slower on purpose.
 *
 * It reaches what arithmetic cannot: roles, labels and heading order. Colour is held instead by
 * `tests/Feature/ContrastTest.php`, which blocks — this suite does not, a boundary inherited
 * from the quality-gate effort and not reopened here.
 *
 * The impact level is passed rather than left to the plugin's default, because "serious and
 * above" is a decision this repository made and not one that should move with a dependency's
 * release. Level 1 is `serious`; level 0 would let a serious defect through.
 */

it('serves the home page to a browser without console output, script errors or accessibility defects', function (): void {
    visit(route('home'))
        ->assertNoConsoleLogs()
        ->assertNoJavaScriptErrors()
        ->assertNoAccessibilityIssues(level: 1)
        ->assertSee('Livewire Starter Kit');
});
