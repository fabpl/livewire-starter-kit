---
name: quality-gate
description: Run this repository's quality gate and repair what it reports. Use at the end of every implementation before reporting work as done, and whenever `composer test` fails — it defines which repairs are allowed and which are forbidden.
---

# Quality Gate

This repository holds every change to one command:

```
composer test
```

It clears configuration, then runs the forbidden-annotation check, Pint, Prettier, Rector and
Larastan, and finishes with the test suite and its coverage threshold. Continuous integration
runs `composer ci:check`, which is this same command and nothing else — so a green run here
and a green pipeline cannot diverge.

**Work is not done until this command exits zero.** Not "done apart from formatting", not
"done, tests to follow". Report work as complete only after a clean run.

The one prerequisite beyond an install is a coverage driver, without which the threshold cannot
be evaluated at all. The pipeline installs pcov; a local machine running Xdebug needs its
coverage mode switched on — `XDEBUG_MODE=coverage composer test`, or `xdebug.mode=coverage` in
`php.ini`. Pest reporting that coverage could not be obtained is that prerequisite missing
rather than a check failing, and it is the only message in this chain that says nothing about
the code. It is not an exception to the rule above: enable the driver and run the command
again, because a run that could not measure coverage has not been run.

## When to run it

- At the end of every implementation, before reporting anything as done.
- After any change to a configuration file in the chain.
- Before proposing a commit message.

Never report a task complete on the strength of a partial run. `composer test` is ordered by
increasing cost, so an early failure means the expensive checks never executed — a Pint
failure tells you nothing about coverage.

## The instruments beside the gate

Two commands sit next to `composer test` rather than inside it. Neither is part of it, and the
pipeline runs neither. Everything the Forbidden list below rules out applies to both exactly as
it applies to the gate.

```
composer mutate
```

It mutates the application code and reports how much of it the suite notices — the question
coverage cannot answer, since a line can be traversed by a test that asserts nothing about it.
It carries its own minimum and needs the same coverage driver `composer test` does.

Run it after writing tests, when you want to know whether they assert anything. A failure here
is not a broken gate: it is the instrument reporting that the suite executes code it does not
check, and the repair is the missing assertion.

```
composer browser:test
```

It builds the frontend assets, then drives the application through a real browser. Every other
check in the chain stops before the page executes, so this is the only place a template that
renders and then throws, or a script that fails to boot, is visible. It needs a browser binary,
which is a download rather than an install and is deliberately not part of `composer setup`:

```
composer browser:install
```

Run it when you have changed a view, a script or a stylesheet, and before trusting that a page
works rather than merely responds. A failure here is a defect in the page, and the repair is in
the page — never in the assertion. What its console assertion actually sees is recorded in the
spec, and it is less than its name suggests.

Both commands name the suites they run. `mutate` and `test` name `Unit` and `Feature` so that no
browser scenario reaches either the coverage measurement or the mutated perimeter; `browser:test`
names `Browser` alone. Those names are load-bearing — see the Forbidden list.

Nothing triggers either command, so both are things that can quietly stop working. That is a
known and accepted cost, recorded in the spec.

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
- Dropping a browser scenario's console assertion, or removing the `--testsuite` names from
  `test` or from `mutate`. Those names are what keep a line a browser walked past from counting
  towards the coverage minimum and from entering the mutated perimeter.

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
| Coverage and mutation perimeter | `phpunit.xml`, `<source>` |
| Mutation minimum | `composer.json`, the `mutate` script |
| Which suites each command runs | the `--testsuite` names in `composer.json` |
| The suites themselves | `phpunit.xml`, `<testsuites>` |
| Browser scenarios | `tests/Browser/` |
| Architecture expectations | `tests/Feature/ArchitectureTest.php` |
| Closures forbidden as route actions | `tests/Feature/RoutingTest.php` |
| Primitives forbidden from reaching the application | `tests/Feature/PrimitivePurityTest.php` |
| Contrast thresholds over the declared Tokens | `tests/Feature/ContrastTest.php` |
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
