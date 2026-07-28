# 06 — Tighten Pint and reformat the tree

**What to build:** formatting stops being advisory. Every file declares strict types, loose
comparisons are rewritten as strict ones, imports are ordered mechanically, and commentary
that says nothing disappears. A developer no longer discusses any of it in a review, because
none of it survives to the review.

This is the second wide mechanical sweep, and like the first it lands green in one pass — it
is a fixer, not a breaking change. Measured in advance: strict-type declarations alone reach
26 of the 30 PHP files, so the diff is large by design, and reviewing it means reviewing the
rules rather than the lines.

One rule deserves attention before it runs, because its name understates it. The annotation-only
rule does not tidy docblocks, it **forbids comments**: any block or line comment carrying no
annotation is deleted unless marked with one of three escape prefixes. Measured on the current
tree it removes docblocks whose entire content restates the method name, and a commented-out
import, while keeping every typed annotation. That is the intent, not a side effect.

Three exclusions are load-bearing. Class finality and return-type insertion belong to Rector.
Native function invocation conflicts directly with name importing — one wants a leading
separator where the other wants an import statement — so it stays off and name importing is
limited to classes.

**Blocked by:** 05

**Status:** resolved

- [x] The framework's own preset is kept and the agreed rules are added on top
- [x] Class finality, return-type insertion and native function invocation are absent
- [x] Name importing is limited to classes
- [x] The whole tree is reformatted and committed
- [x] Running Pint a second time produces no further change
- [x] Running Rector and then Pint produces no further change — the chain has a fixed point
- [x] No path was excluded from Pint in order to make the run pass
- [x] A comment carrying real explanation can still be kept, using an escape prefix, and this is demonstrated
- [x] `composer test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

The rule that forbids comments is Pint's own `Pint/phpdoc_type_annotations_only`, not a
PHP CS Fixer rule — it is a custom rule Pint ships under a `Pint/` prefix, and it is off by
default. **The three escape prefixes the spec names are a documentation convention, not a
mechanism.** `@note`, `@warning` and `@todo` are what Pint's manual tells you to write, but the
implementation has no list of prefixes: `TypeAnnotationsOnlyFixer::processComment()` returns
early on `str_contains($content, '@')`, so any `@` at all preserves a comment — an email address
in an example, a stray literal. The prefixes are therefore worth writing because a reader
recognises them, not because the tool enforces them, and the hatch is wider than the spec
assumes. The exemption for `config/` is real and mechanical, in the rule's own `supports()`
method rather than in this repository's configuration, so it cannot be undone here and did not
need to be requested.

**That mechanism decides how a preserved comment has to be written, and the difference is not
cosmetic.** A `//` block is a separate token per line, so `// @note` preserves that one line and
the rule deletes every line under it. A `/* … */` block is a single token, so one `@note`
anywhere inside preserves the whole thing. A `/** … */` docblock is worse than either: the rule
rebuilds it from its annotation lines, so prose inside one is lost even with a prefix.
Explanation that runs to more than a line therefore has to be a `/* @note … */` block.

`rector.php` is where this was demonstrated, and not by contrivance. Ticket 05 left six
multi-line `//` blocks in that file explaining why particular rules are skipped, and this
ticket's rule would have deleted all six on its first run — the file sits inside Pint's
perimeter like any other. They are now `/* @note … */` blocks, unchanged in wording, and Pint
leaves the file alone. This is the intended shape of the escape hatch: preserving that reasoning
took a deliberate edit that shows up in a diff, which is exactly what the spec asks the prefixes
to cost.

**One spec-named rule was removed after measurement: `date_time_immutable`.** It is a second
owner for a concern Rector's Carbon set already owns, and the two disagree about the
destination rather than merely overlapping. Measured on a probe: given `new DateTime('now')`,
`new DateTimeImmutable('now')`, `date('Y-m-d')`, `date('Y-m-d', $ts)` and `time()`, Rector's
Carbon set rewrites all five, to `Carbon::now()`, `CarbonImmutable::now()`,
`Carbon::now()->format(…)`, `Carbon::createFromTimestamp($ts)->format(…)` and
`Carbon::now()->getTimestamp()`. Pint's rule rewrites the first three towards
`DateTimeImmutable` instead, and adds a `use DateTimeImmutable;` import while it is there.

In the fixed order the chain still converged, but only because Rector always got there first
and consumed everything Pint's rule would have touched — so the rule could never do anything
except in the one case where it does damage. Run Pint alone, as `composer lint` does, and it
produces `new \DateTimeImmutable('now')` plus a dead import that the next Rector run rewrites to
`CarbonImmutable::now()`: a tree the fix command left dirty and the check command rejects. The
spec's own ownership rule settles it — "no concern has two owners" — and ticket 05 set the
precedent four times in the other direction, taking rules out of Rector because the concern was
Pint's. This is the same move with the tools swapped. Dropping it costs nothing, since the
concern keeps an owner and that owner is the one this stack actually wants: Laravel dates are
Carbon.

Two further overlaps were probed and are genuinely harmless, so nothing was changed for them.
`no_useless_else` reaches the same result as `RemoveAlwaysElseRector` and `SimplifyIfReturnBoolRector`
by the same direction of travel, and Rector merely gets there first. `strict_param` adds the
strict flag to `in_array` calls Rector has already finished with; `SingleInArrayToCompareRector`
was verified to match the three-argument form as readily as the two-argument one, so Pint cannot
hand Rector back a call it wants to rewrite.

Where the framework's preset already satisfies a concern the spec lists, the rule was not
restated. Import order, nullable-type normalisation, `heredoc_to_nowdoc`, `single_quote` and
`no_useless_return` are all in the Laravel preset at settings this effort agrees with, and
copying them into `pint.json` would put the same decision in two places. Two entries are
additions to what the preset carries rather than duplicates: `no_superfluous_phpdoc_tags` is
restated with `allow_mixed` and `allow_unused_params` turned off, which is the preset's rule
made strict, and `global_namespace_import` handles the root-namespace case that the preset's
`fully_qualified_strict_types` does not.

**`artisan` was outside Pint's reach and is now inside it.** The spec's scope is "everything,
without exception, including the files the kit shipped", but Pint's finder matches `*.php` and
`artisan` carries no extension, so it had been silently exempt since the kit was installed —
still holding the two narration comments `public/index.php` lost under this rule, and no strict
types. It cannot be brought in from `pint.json`: Pint reads only `exclude`, `notPath` and
`notName` from that file, all of them narrowing, and offers no way to widen the finder. So the
perimeter is widened where it can be, in the `lint` and `lint:check` scripts, which now pass
`. artisan` explicitly. That is the opposite of the forbidden repair — nothing was excluded to
make the run pass; a file that had been escaping the gate was pulled into it, and it needed
three fixers when it arrived.

Rector still does not reach `artisan`, and that is left alone rather than fixed here. Rector
filters by file extension before it filters by path, so widening its perimeter is not a matter
of adding the file to `withPaths()`; and Rector's perimeter is ticket 05's configuration, which
this ticket has no business editing for a second reason on the same commit. The asymmetry is
harmless today — every rewrite Rector would want there is already in place — but it is a real
gap and belongs in a ticket of its own.

The three load-bearing exclusions have no home in the configuration, because `pint.json` is JSON
and cannot hold a comment. `final_class`, `phpdoc_to_return_type`, `void_return` and
`native_function_invocation` are absent from both the preset and the rules block, and their
absence is verifiable from the tree: `app/Http/Controllers/Controller.php` and
`tests/TestCase.php` are still not final, and `public/index.php` still calls `file_exists(`
without a leading separator. Since name importing is limited to classes, no `use function`
statement appeared anywhere.

Measured outcome: 28 files rewritten, and every PHP file the chain owns now declares strict
types — 29 of the 30 tracked `*.php` files plus `artisan`, the thirtieth being a Blade template
that Pint excludes by design and Prettier owns. The comment rule removed exactly what the spec predicted: thirteen docblocks
whose entire content restated the name of the thing below them, two more trimmed to the
annotation they carried, the three narration comments in `public/index.php` and the two in
`artisan`, the commented-out
`MustVerifyEmail` import in `app/Models/User.php` and the commented-out `User::factory(10)` call
in the seeder. Every typed annotation survived, including the model's `@property` block, its
`@use HasFactory<UserFactory>` and the `@return array<string, string>` on `casts()`. `ordered_class_elements` moved `initials()`
above `casts()`; `global_namespace_import` turned the two `#[\Override]` attributes into an
import. Nothing was excluded from Pint's perimeter, and `composer fix` on the committed tree
changes no file.

## Follow-up — code review of tickets 05, 06 and 07

A review of the three configurations against the spec found that this ticket's two most
consequential findings never left it. Both were decided correctly here and recorded here, and
the spec — which is the document a reader consults — went on saying the opposite. They are now
written into it.

**`date_time_immutable` is gone from Pint and the spec still listed it** among the rules Pint
adds. The measurement above is the reason, and it is a stronger one than a mere overlap: Rector's
Carbon set and Pint's rule disagree about the destination. The spec's Pint section now names the
exclusion as load-bearing alongside the other three, and the ownership table gains the row.

**The three escape prefixes are a documentation convention, not a mechanism**, as the analysis of
`TypeAnnotationsOnlyFixer::processComment()` above establishes. The spec described a list of three
markers; the implementation preserves any comment containing an `@`. That is a wider hatch than
the spec claimed, and a spec that understates its own escape route is the one kind of error this
effort cannot afford. The section now states the mechanism, and carries the consequence for how a
preserved comment has to be written — a distinction this ticket discovered and the spec never had.

The `artisan` gap this ticket says "belongs in a ticket of its own" did not get one. It is instead
stated in the spec, in Rector's section, as a permanent property of the tool rather than an item of
work: Rector filters by extension before path, so the file cannot be brought in at all. The
analyser now names it explicitly, which leaves structural rewriting the only part of the chain it
escapes.
