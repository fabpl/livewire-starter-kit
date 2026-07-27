# 07 — Raise static analysis to its strictest setting

**What to build:** Larastan moves from a middling level to the strictest position it can
express, across a widened perimeter that includes the tests, with no mechanism for filing an
error away. A developer gets type defects reported before runtime, and no way to silence one.

Two facts make this cheaper than it sounds, both measured in advance. The maximum level
already passes with zero errors on the current tree, so the raise itself is free. And widening
the perimeter to the tests, the public entry point and the whole bootstrap directory costs one
error — in an example test that the Pest migration has already replaced by the time this runs.

The level is pinned as an integer rather than written as the "maximum" alias. The alias means
"the highest that exists", so a release adding a level would turn the pipeline red on a
dependency update nobody connected to it. Raising the bar should be a commit.

Two official extensions are added because they live **beyond** the maximum level and no level
contains them — they are the part that actually bites.

One cost here is **not** measured, and this ticket must not paper over it: the noise
`phpstan-strict-rules` produces on idiomatic Laravel code could not be evaluated before
installation. If the result is unreasonable, remove the extension and record the removal and
the measurement in the spec. Do not keep it and suppress what it reports.

**Blocked by:** 05

**Status:** ready-for-agent

- [ ] The level is pinned as an integer at the current maximum, not written as the alias
- [ ] The strictness and deprecation extensions are installed and active
- [ ] The perimeter covers the application, bootstrap, configuration, database, routes, tests and public entry point
- [ ] No baseline file exists and none is referenced
- [ ] Every reported error is fixed in the code — no ignore annotation, no inline type override, no cast or widened signature added to silence one
- [ ] If the strictness extension is removed, the reason and the measurement are recorded in the spec
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
