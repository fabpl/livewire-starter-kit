# 05 — Migrate the test suite to Pest

**What to build:** Pest 4 replaces PHPUnit 12 as the runner, and every test is written in its
functional style. No test class remains in the tree apart from the base case the suites bind
to.

The migration costs three files today and grows with every test written afterwards, which is
the whole reason it happens now. Pest wraps PHPUnit rather than excluding it: `phpunit.xml`
is still read and `phpunit/phpunit` stays as a transitive dependency.

One defect gets corrected on the way. `tests/Unit/ExampleTest.php` extends
`PHPUnit\Framework\TestCase` while importing `RefreshDatabase`; without a Laravel application
there is no `setUpTraits()`, so the trait is inert. It is decoration that misstates what the
test does, and it disappears with the rewrite rather than being carried across.

**Blocked by:** 04

**Status:** ready-for-agent

- [ ] `pestphp/pest` and `pestphp/pest-plugin-laravel` are dev dependencies
- [ ] `tests/Pest.php` binds `Tests\TestCase` to the suites
- [ ] Both example tests are rewritten in functional style
- [ ] The inert `RefreshDatabase` on the unit example is gone rather than ported
- [ ] No test class remains under `tests/` apart from `Tests\TestCase`
- [ ] `composer test` invokes Pest
- [ ] The root-route test still proves the application boots, routes and renders
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
