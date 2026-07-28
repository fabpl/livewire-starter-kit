---
name: quality-gate
description: Run this repository's quality gate and repair what it reports. Use at the end of every implementation before reporting work as done, and whenever `composer test` fails — it defines which repairs are allowed and which are forbidden.
---

# Quality Gate

This repository holds every change to one command:

```
composer test
```

It clears configuration, then runs Pint, Prettier, Rector, Larastan, the forbidden-annotation
check, the test suite and the coverage threshold. Continuous integration runs
`composer ci:check`, which is this same command and nothing else — so a green run here and a
green pipeline cannot diverge.

**Work is not done until this command exits zero.** Not "done apart from formatting", not
"done, tests to follow". Report work as complete only after a clean run.

## When to run it

- At the end of every implementation, before reporting anything as done.
- After any change to a configuration file in the chain.
- Before proposing a commit message.

Never report a task complete on the strength of a partial run. `composer test` is ordered by
increasing cost, so an early failure means the expensive checks never executed — a Pint
failure tells you nothing about coverage.

## Repairing a failure

### Allowed

1. **`composer fix`** — runs Rector, then Pint, then Prettier, in that order. This is what
   the fixers are for; let them work before touching anything by hand. The order matters:
   Rector emits code in its own formatting and Pint normalises afterwards.
2. **Write the missing tests.** A coverage failure means untested code, not a threshold that
   is too high.
3. **Fix the code the analyser points at.** Larastan reports a real type defect far more
   often than a false positive. Read the error identifier's documentation page before
   deciding otherwise.
4. **Delete what should not exist.** A comment the formatter strips, an unused import, a
   dead branch — removal is a repair.

Re-run `composer test` after any repair. The committed tree must be a **fixed point** of the
whole chain: running the tools again changes nothing.

### Forbidden

Every item below makes the gate pass without making the code better. Each one is what a
stuck agent reaches for. None is available here.

- Lowering any threshold — the coverage `--min`, the Larastan level, a mutation score.
- `@codeCoverageIgnore` or `@pest-mutate-ignore`, in any form. A dedicated check in
  `composer test` fails on their presence anywhere in the tree.
- A PHPStan baseline, whether generated or hand-written.
- `@phpstan-ignore`, `@phpstan-ignore-next-line`, an inline `@var` used to override inferred
  types, or a cast added only to silence an error.
- Widening a parameter or return type to make an error go away.
- Adding `ignoring()` to an architecture preset.
- Adding paths to `exclude`, `notPath` or `notName` in `pint.json`, or to `.prettierignore`.
- Disabling a Pint rule or a Rector set.
- `@note`, `@warning` or `@todo` used to preserve a comment that should simply be deleted.
  Those prefixes exist for explanation the code genuinely cannot carry, not as an escape from
  the formatter.
- Deleting, skipping or marking incomplete a test that fails — including Pest's `->skip()`
  and `->todo()`.

**If you believe a rule is genuinely wrong, say so to the user and stop.** Legitimate
disagreement with the gate is resolved by the human changing the configuration deliberately,
never by an agent routing around it. Raising the objection is correct behaviour; silently
weakening the gate is not.

## Where the rules actually live

This file deliberately does not restate them. The configuration is the normative source, and
unlike prose it executes:

| Concern | File |
| --- | --- |
| Formatting rules | `pint.json` |
| Structural rewrites | `rector.php` |
| Static analysis level, paths, extensions | `phpstan.neon` |
| Coverage perimeter | `phpunit.xml`, `<source>` |
| Architecture expectations | `tests/Feature/ArchitectureTest.php` |
| Closures forbidden as route actions | `tests/Feature/RoutingTest.php` |
| Frontend formatting and its boundary | `.prettierrc`, `.prettierignore` |
| Forbidden suppression annotations | `bin/check-annotations.php` |
| The chain itself | `composer.json`, `scripts` |

If this file and a configuration file disagree, the configuration file is right and this one
needs correcting.

The reasoning behind each rule is in `.scratch/enforced-quality-gate/spec.md`, and the two
decisions that shaped the chain are in `docs/adr/`.

## Self-check before reporting done

- [ ] `composer test` was run to completion, not interrupted
- [ ] It exited zero
- [ ] No threshold, level or perimeter was altered to reach that result
- [ ] No suppression annotation, baseline or ignore entry was added
- [ ] No failing test was skipped, deleted or marked incomplete
- [ ] Re-running the chain produces no further changes
- [ ] Any objection to a rule was raised with the user rather than worked around
