# 05 — Install Rector and rewrite the tree

**What to build:** structural rewriting joins the chain. Dead code is removed, type
declarations are added, classes that nobody extends become final, and the code is lifted into
the constructions the declared language version offers. A developer gets a fix command that
performs all of it and a check that fails when the committed tree is not already in that
state.

This is a wide mechanical sweep rather than a vertical slice, and it does not need
expand–contract: Rector is a fixer, so it produces a self-consistent tree in one pass and
breaks no call sites.

It runs before Pint because it emits code in its own formatting and Pint normalises
afterwards — the reverse order leaves the tree dirty. It runs before the static-analysis
raise because the type declarations it adds are what make the strictest level reachable
without hand-written annotations. It runs after the Pest migration because its test-framework
rule groups are excluded on the grounds that no test classes will exist, and migrating first
makes that a fact rather than an anticipation.

The rule groups to enable and, more importantly, the ones to leave off are settled in the
spec. Two exclusions carry reasoning that should not be undone casually: the naming group is
the only group in the whole chain that rewrites identifiers, and the docblock type group would
make Rector a second docblock writer alongside Pint.

**Blocked by:** 01, 04

**Status:** resolved

- [x] Rector and its Laravel rule package are development dependencies
- [x] Exactly the agreed rule groups are enabled and no others
- [x] The coding-style, naming, docblock-type, named-argument and test-framework groups are absent
- [x] The language target and the framework version are both resolved from the dependency manifest rather than pinned by hand
- [x] Rector's perimeter matches the tree the rest of the chain covers
- [x] A fix command applies rewrites and a check command reports without writing
- [x] The aggregate command runs the check form and fails on a tree Rector would change
- [x] The whole tree is rewritten and committed
- [x] Running Rector a second time produces no further change
- [x] `composer test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

The spec's nine rule groups map one to one onto `withPreparedSets()` arguments, and the five
groups it names as excluded are all real arguments left at their default. The mapping is worth
recording because two of the spec's names are paraphrases: "conditional simplification" is the
`if` group, and "a deprecated group the tool itself warns about" is `strictBooleans`, which
Rector 2.5 prints a warning for and tells you to replace with the code-quality and coding-style
sets. Framework and language targets both resolve from `composer.json`: `withPhpSets()` with no
argument reads `require.php`, and `withComposerBased(laravel: true)` loads only the sets whose
composer trigger matches an installed package — the Laravel version sets, Faker and Livewire 4.
The Laravel provider's untriggered sets, among them "replace facades with service injection",
are not loaded by that call.

**The ownership table's line on class finality does not hold in this version, and nothing was
done to force it.** Rector 2.5 ships no rule that finalises classes without children — the rule
the spec assumed was removed before this release, and the only finality rule that remains is
`FinalizeTestCaseClassRector`, which the recommended set already carries and which has nothing
to act on now that no test classes exist. So no class in the tree became final under this
ticket. The spec's reasoning for keeping finality away from Pint is untouched by this; what
changes is that the *verifier* named in that table — the architecture assertions of ticket 08 —
is now the only thing that will enforce finality, with the fix left to hand.

**Four rules were taken out because the concern they act on belongs to Pint.** Enabling a group
is not the same as accepting every rule inside it: the spec settles ownership per concern, and
the enabled groups reach across that line in four places. The audit was done against the
419 rules the configuration actually registers rather than against the group names.

- `DeclareStrictTypesRector` (recommended set) and `SafeDeclareStrictTypesRector` (code quality)
  write `declare(strict_types=1)`, which the table gives to Pint and which the spec measures at
  26 of 30 files under the Pint section. Left enabled they were rewriting 20 files here, in the
  wrong ticket. Worse, the safe variant declines files it judges risky, so it left
  `public/index.php` and `tests/Feature/ExampleTest.php` without the declaration — a tree
  covered unevenly is the visible symptom of a concern owned by the wrong tool.
- `UseIdenticalOverEqualWithSameTypeRector` (code quality) rewrites `==` to `===`. Strict
  equality is Pint's row. Rector's version is the more careful of the two, acting only where it
  can prove the operand types match, but the spec chose Pint's — which acts on every comparison
  — and a concern with a careful owner and a thorough one still has two owners.
- `PostIncDecToPreIncDecRector` (recommended set) is a coding-style rule carried in past the
  coding-style group being switched off, and it is the one that does not merely overlap: it
  rewrites `$scanned++` to `++$scanned` in `bin/check-annotations.php` while Pint's
  `increment_style` rewrites it straight back. Run twice, `composer fix` produced the same two
  edits again — precisely the non-terminating aggregate command the ownership rule exists to
  prevent.

What the audit found clean is worth recording too, since it is the reason nothing further was
touched. Rector never imports names — `withImportNames()` is off by default — so import order
and name importing stay wholly Pint's. No enabled rule adds a prose docblock: the twelve
docblock rules that remain either remove tags, which converges with Pint's superfluous-tag
removal rather than fighting it, or read docblocks to infer native types, and the one rule that
writes a `@param` only acts on Rector's own rule classes. The two concatenation rules are
code-quality simplifications, not the quoting and heredoc consistency Pint owns.

`bootstrap/cache` had to leave the perimeter for a different reason: the framework regenerates
it on every package discovery, git ignores it, and Rector rewrote its two generated files
wholesale. Left in, `composer setup` followed by `composer test` would fail in the pipeline on
files this repository does not own. Pint excludes the same directory.

What the rewrite actually did, once the four rules were out, is nine files: return types on
closures and arrow functions, `#[\Override]` on the two overridden methods, and one early
return. That is the whole of Rector's own row in the table on a tree this size.

Rector's cache is written to `.rector.cache` beside the analyser's rather than to the system
temporary directory, where every checkout on a machine would share one directory. The check was
verified against a cold cache as well as a warm one, and verified both to fail on a tree Rector
would change and to write nothing when it does.

## Follow-up — code review of tickets 05, 06 and 07

A review of the three configurations against the spec found two things this ticket left open,
and both are now closed in a later commit rather than here.

**One rule of the recommended set was carried in past a group that is off.**
`AddSeeTestAnnotationRector` stamps a `@see` docblock on every class pointing at its test —
Rector writing docblocks, which is the conflict the docblock-type group was disabled to avoid,
arriving through a different door. Four rules from that set were already named individually in
`withSkip()`; this one had been missed. It was inert on this tree only because the functional
test style leaves no test classes for it to pair a class with, so nothing failed and nothing
would have until the first test class existed. It is now skipped, with the ownership reasoning
next to it.

**The perimeter did not include `rector.php` itself.** The configuration file was reaching Pint
and no other tool — the file that tells Rector to rewrite the tree being the one file Rector did
not rewrite, and the newest PHP in the repository going to no analyser at all. It is now inside
`withPaths()` and inside the analyser's paths. The checkbox above claiming the perimeter matched
the rest of the chain was therefore ticked ahead of being true; `artisan` remains outside, for
the extension reason ticket 06 records, and that gap is now stated in the spec rather than left
to a ticket that was never opened.
