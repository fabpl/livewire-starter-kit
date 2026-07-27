# 07 — Require full coverage of the application code

**What to build:** every line of `app/` is executed by a test, the requirement is enforced by
the aggregate command, and the two annotations that would let anyone escape it are made
unusable.

This is the only ticket in the effort that is writing work rather than configuration, and its
size is genuinely unknown in advance. Two costs are known from reading the code.
`User::initials()` carries a ternary whose second branch a single test would not exercise.
`AppServiceProvider` registers a password-defaults closure whose production branch never runs
under `APP_ENV=testing` — and which is only invoked at all when a password validation
happens — so reaching it means deliberately faking production.

The perimeter stays `app/` and is not widened. Routes are declarations and migrations are
single-use scripts; requiring coverage there would produce tests asserting that the framework
can route and migrate. The escape that leaves open — logic hidden in a route closure, outside
the measured perimeter — is closed by ticket `06`, which prohibits the logic rather than
measuring it.

Nothing in the chain watches for `@codeCoverageIgnore` or `@pest-mutate-ignore`, so the
prohibition needs its own check. It is a composer script rather than a line buried in the
workflow, so that it runs locally exactly as it runs in CI.

**Blocked by:** 05

**Status:** ready-for-agent

- [ ] `composer test` runs `pest --coverage --min=100`
- [ ] `<source>` in `phpunit.xml` covers `app/` and nothing wider
- [ ] Both branches of `User::initials()` are exercised
- [ ] The production branch of the password-defaults closure is exercised
- [ ] A composer script fails when `@codeCoverageIgnore` or `@pest-mutate-ignore` appears anywhere in the tree
- [ ] That script is part of `composer test` and runnable on its own
- [ ] The CI workflow installs `pcov`
- [ ] Coverage reaches 100% with no suppression and no lowered threshold
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
