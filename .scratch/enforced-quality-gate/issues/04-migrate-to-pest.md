# 04 — Migrate the test suite to Pest

**What to build:** the suite runs on Pest, and every test is written in its functional style.
A developer writing a test here has one way to do it, not two, and the root route is still
covered by a test proving the application boots, routes and renders.

This is early rather than late for two reasons. It changes the layer that architecture,
coverage and mutation are all built on, so everything downstream should be written against
its final shape. And it removes a placeholder that would otherwise be repaired under the
static-analysis ticket and replaced immediately afterwards — work thrown away.

One shipped defect gets corrected on the way rather than carried across. An example test
extends the bare test case while importing the database-refresh trait; with no application
behind it there is no trait setup, so the trait does nothing at all. It is decoration that
misstates what the test does.

Pest wraps the previous runner rather than excluding it, so the existing test configuration
is still read and the previous runner remains as a transitive dependency.

**Blocked by:** 01

**Status:** ready-for-agent

- [ ] Pest and its Laravel plugin are development dependencies
- [ ] The suites are bound to the application test case, so feature tests still boot the application
- [ ] Both example tests are rewritten in functional style
- [ ] The inert database-refresh trait is gone rather than ported
- [ ] No test class remains apart from the base case the suites bind to
- [ ] The aggregate command invokes Pest
- [ ] The root-route test still proves the application boots, routes and renders
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
