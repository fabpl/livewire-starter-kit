# 01 — Raise the language floor to PHP 8.5

**What to build:** a developer can no longer write code that their machine accepts and the
pipeline rejects. One language version is declared, and the dependency manifest, the
pipeline and the static analyser all say the same thing.

Today they say three different things in effect: the manifest constrains 8.3, the pipeline
installs 8.3, and the development machine runs 8.5. Syntax introduced after 8.3 passes
locally, reaches the pipeline, and fails there — with nothing in between to catch it.

This is first because Rector reads its language target from the manifest. Raising the floor
afterwards would mean rewriting the tree a second time.

**Blocked by:** None — can start immediately.

**Status:** resolved

- [x] The dependency manifest requires PHP 8.5 or later
- [x] The pipeline installs PHP 8.5
- [x] Larastan is told to analyse against 8.5, so it rejects locally what the pipeline would reject
- [x] The lockfile is regenerated and committed
- [x] The framework and every development dependency still resolve at that floor
- [x] Syntax newer than the declared floor is demonstrably rejected by static analysis
- [x] `composer test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention
