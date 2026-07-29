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
