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

**Status:** resolved

- [x] The level is pinned as an integer at the current maximum, not written as the alias
- [x] The strictness and deprecation extensions are installed and active
- [x] The perimeter covers the application, bootstrap, configuration, database, routes, tests and public entry point
- [x] No baseline file exists and none is referenced
- [x] Every reported error is fixed in the code — no ignore annotation, no inline type override, no cast or widened signature added to silence one
- [x] The strictness extension is kept, and its measurement is recorded in the spec
- [x] `composer test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

The maximum for PHPStan 2.2 is level 10, so `level: 10` is what the integer pins. The two
extensions are included as `rules.neon` from each package rather than enabled by a parameter,
which is how they ship; neither is reachable from any level, which is the whole reason they are
named separately.

The unmeasured cost the ticket refused to paper over turned out to be small. The whole raise
reports five errors, and the strictness extension owns three of them: `array_filter` called
without a callback, once in `config/app.php` and twice in `config/database.php`. That is not
noise. Both sites mean "drop the entries that carry no value", and the one-argument form does
something broader: it drops everything falsy, so the string `"0"` was being discarded at both
sites. All three take `filled(...)` as the second argument, Laravel's own predicate, which says
what the loose semantics only implied. The two predicates are not equivalent, in both directions
— `filled('0')` is true, so `"0"` now survives, and `filled()` trims, so a previous key that is
nothing but whitespace is now dropped where the falsy test kept it. Both differences move toward
the stated intent.

The remaining two errors are one line of the shipped feature test, `$this->get('/')->assertOk()`.
The analyser resolved `$this` to `Pest\PendingCalls\TestCall` and was right to: the closure
handed to `it()` is not a method, and Pest rebinds it at runtime through a mechanism no static
analyser follows. The fix is the functional style the spec already requires everywhere else —
`Pest\Laravel\get()`, whose docblock return type the analyser does follow. The error was a real
report about a construct that only works by runtime rebinding, not a false positive to be
tolerated.

Perimeter and level were both verified active rather than assumed, by probe files that were
analysed and then deleted: a non-boolean `if` condition and an `in_array` without its third
argument for the strictness extension, a call to a `@deprecated` function for the deprecation
extension, and an out-of-range array offset for the level itself — each reported, and each
reported inside `tests/`, which is what proves the widened perimeter reaches the largest body of
code in the project.

`bootstrap/` enters whole, unlike Rector and Pint, which both exclude `bootstrap/cache`. The
asymmetry is deliberate and safe: those two *rewrite* the generated files and would compete with
the framework that owns them, whereas the analyser only reads. It was run with the generated
files present, and again after `php artisan optimize` had added four more of them, and reports
nothing on any.

A second asymmetry is recorded rather than resolved, because resolving it would go past what was
asked. The ticket says "public entry point", so the analyser lists `public/index.php`; Rector
lists `public` whole. They cover the same file today, since that directory holds no other PHP,
but a second one added there would be rewritten and not analysed. Widening the analyser to
`public` would close it in one word — a deliberate change for a human to make, not one to take
in passing on a ticket that named the file.

No baseline file exists, none is referenced, and none was generated at any point — including as
a temporary measure while fixing the five errors.
