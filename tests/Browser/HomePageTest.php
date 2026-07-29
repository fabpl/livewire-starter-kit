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
        ->assertSee('cannot be quietly weakened');
});

/*
 * @note The one behaviour on this page the blocking suite cannot see. The HTTP seam proves the
 * control is rendered; only a browser has a clipboard and an Alpine runtime.
 *
 * The assertion is on the visible confirmation rather than on the clipboard's contents, and that
 * is the honest target rather than the convenient one: a reader who was not told cannot know the
 * write succeeded, so a green clipboard over a silent page would be a passing test on a broken
 * control.
 *
 * The permission is granted to the context because Playwright's Chromium withholds it and a real
 * Chrome does not — it grants `clipboard-write` to the focused document without prompting. Left
 * at the default, `writeText` rejects with `NotAllowedError` and this test fails on a control
 * that works everywhere a reader would meet it, so the grant removes a difference between the
 * harness and the browser rather than relaxing anything about the page.
 *
 * Worth knowing before trusting the suite too far, and measured rather than assumed: when that
 * rejection happens, neither `assertNoConsoleLogs` nor `assertNoJavaScriptErrors` sees it. An
 * unhandled promise rejection reaches neither. This assertion on the confirmation is therefore
 * the only thing standing between a broken copy control and a green suite.
 */
it('confirms visibly that the installation command was copied', function (): void {
    visit(route('home'), ['permissions' => ['clipboard-write']])
        ->click('Copy')
        ->assertSee('Copied');
});

/* @note The theme switch is the one piece of hand-written JavaScript in this tree, so the three
   tests below observe it end to end rather than trusting it. What is asserted is the class on the
   document element, because that is the whole of what the switch is for: the stylesheet's dark
   scope hangs off it, and a control that stored a preference without moving that class would be
   a switch that switches nothing. */

/*
 * @note Every visit here emulates a dark operating system, and that is what gives the sequence
 * its teeth rather than being a detail of setup. Under a light one, "light", "dark" and "system"
 * would produce the same page for two of the three states, and an assertion that cannot tell two
 * states apart is not observing a three-state control.
 *
 * The order is chosen so that every assertion follows a *change*. The page loads dark, so light
 * is asserted after a transition into it, dark after a transition back, and light again before
 * system — which means the final assertion distinguishes system from the light it was just in,
 * rather than agreeing with a state the page was already in. No step here can pass by accident of
 * the initial condition.
 */
it('applies each of its three theme states', function (): void {
    visit(route('home'))
        ->inDarkMode()
        ->click('Light')
        ->assertScript("document.documentElement.classList.contains('dark')", false)
        ->click('Dark')
        ->assertScript("document.documentElement.classList.contains('dark')")
        ->click('Light')
        ->assertScript("document.documentElement.classList.contains('dark')", false)
        ->click('System')
        ->assertScript("document.documentElement.classList.contains('dark')");
});

/*
 * @note Persistence, and the seam it is observed at is the reload rather than local storage.
 * Asserting the stored value would be the implementation restated; asserting that a dark system
 * still serves a light page proves the whole chain — the control wrote, the head script read, and
 * it applied the choice before the first paint rather than the operating system's preference.
 *
 * The pressed state is asserted after the reload for the second half of that chain: the control
 * has to come back up showing the choice it is honouring, or a reader is told the page is
 * following their system while it is not.
 */
it('remembers the reader\'s choice across a reload', function (): void {
    visit(route('home'))
        ->inDarkMode()
        ->click('Light')
        ->refresh()
        ->assertScript("document.documentElement.classList.contains('dark')", false)
        ->assertAriaAttribute('Light', 'pressed', 'true');
});

/*
 * @note The listener, which is the part of the system state that sampling the media query once at
 * load would not give: a reader whose operating system switches at dusk has to be followed there.
 *
 * The event is dispatched by the test, and that is a limitation of the harness rather than a
 * choice. Playwright can emulate a colour scheme when a browser context is created and this
 * plugin exposes that as `inDarkMode`, but neither exposes a way to change it afterwards, so the
 * arrival of the event is the one thing here that is staged. Everything it exercises is real:
 * the media query list is the component's own, reached through Alpine's public `$data` accessor
 * and the control's accessible name, and `matches` reports the genuinely emulated preference.
 *
 * Stripping the class first is what makes the assertion mean something. It puts the page in the
 * position it would be in a moment before a system that was light turns dark; without it the
 * class is already correct and a listener that was never registered would pass. The class is then
 * restored by the code under test or not at all.
 */
it('keeps following the operating system while its system state is active', function (): void {
    $page = visit(route('home'))
        ->inDarkMode()
        ->click('System');

    $page->script(<<<'JS'
        document.documentElement.classList.remove('dark')
        JS);

    $page->script(<<<'JS'
        Alpine.$data(document.querySelector('[aria-label="Colour theme"]')).query.dispatchEvent(new Event('change'))
        JS);

    $page->assertScript("document.documentElement.classList.contains('dark')");
});
