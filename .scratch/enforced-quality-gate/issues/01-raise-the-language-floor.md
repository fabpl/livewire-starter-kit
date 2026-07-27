# 01 — Raise the language floor to PHP 8.5

**What to build:** the repository declares one language version and every tool agrees with
it. Today four places disagree in effect — `composer.json` constrains `^8.3`, the workflow
installs 8.3, the development machine runs 8.5.8, and nothing pins the version static
analysis assumes. Syntax from 8.4 or 8.5 passes locally and would break the pipeline, with
nothing to catch it in between.

This lands first, and the order is the point. Rector reads its PHP target from
`composer.json`, so every rewrite in ticket `02` depends on the floor already being correct.
Raising it afterwards would mean rewriting the tree twice.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] `composer.json` requires `php: ^8.5`
- [ ] The CI workflow installs PHP 8.5
- [ ] `phpstan.neon` declares `phpVersion: 80500`
- [ ] `composer.lock` is regenerated and committed
- [ ] The framework and every dev dependency still resolve at that floor
- [ ] `composer test` passes unchanged
- [ ] Committed as a single commit following the repository's Conventional Commits convention
