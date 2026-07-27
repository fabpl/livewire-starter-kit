# 04 — Raise static analysis to its strictest setting

**What to build:** Larastan moves from level 7 to the strictest position it can express, over
a widened perimeter, with no way to file an error away.

Two facts make this cheaper than it sounds and were measured in advance. The maximum level
already passes with zero errors on the current tree, so the raise itself costs nothing. And
widening the paths to `tests/`, `public/` and the whole of `bootstrap/` costs exactly one
error, in the example test that ticket `05` deletes.

The level is pinned as an integer rather than written `max`. The alias means "the highest
that exists", so a PHPStan release adding a level would turn the pipeline red on a dependency
update nobody connected to it. Raising the bar should be a commit.

The unmeasured part of this ticket, stated plainly: `phpstan-strict-rules` could not be
evaluated before installation, and it has a reputation for noise on idiomatic Laravel code.
If the result is unreasonable, remove it and record the removal in the spec. Do not keep it
and suppress what it reports.

**Blocked by:** 02

**Status:** ready-for-agent

- [ ] `phpstan.neon` pins `level: 10` as an integer, not `max`
- [ ] `phpstan/phpstan-strict-rules` and `phpstan/phpstan-deprecation-rules` are installed and included
- [ ] Paths cover `app/`, `bootstrap/`, `config/`, `database/`, `routes/`, `tests/` and `public/`
- [ ] No baseline file exists and none is referenced
- [ ] Every reported error is fixed in the code — no `@phpstan-ignore`, no inline `@var` override, no cast added to silence, no type widened to silence
- [ ] If `strict-rules` is removed, the reason and the measurement are recorded in the spec
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
