# 05 — Record the placement rule and open the glossary

**What to build:** a contributor who has to install the next framework default finds the rule
that decides where it goes, and the vocabulary to argue about it in. Without this, the four
locations tickets 01 to 04 leave behind read as four arbitrary habits, and the next default
lands in the most obvious place — which is the wrong one about half the time.

The rule is one sentence with two corollaries. A default is installed at the level where its
guard is provable by the suite. A default whose guard would be unprovable descends to the level
where the guard disappears entirely. Where no level makes it provable, it is installed anyway
and the exception is written at the point of call.

That rule is what actually decided the placements: it is why the prohibition on stray requests
sits in the test bootstrap and not in the application service provider, which is where the
obvious reference puts it, and it is the question a future reader will ask first.

It qualifies as a decision record on all three of the project's criteria. Surprising without
context — the dispersal reads as incoherence otherwise, and the well-known package says the
opposite. A genuine trade-off — weighed between a provider holding a guard nothing can prove
and a test bootstrap holding no guard at all. And costly to reverse, not in the two calls it
moves but in the rule, once agents have followed it across twenty defaults.

The glossary does not exist yet and is created here rather than earlier, because it should
name what the work turned out to be rather than what it was expected to be. Four terms, and
they are the ones the ADR needs in order to be stated at all. It stays free of implementation
detail, per the project's domain documentation convention — a glossary, not a specification.

One prior document is superseded rather than contradicted, and saying so is part of the work.
The quality gate spec places a domain glossary out of scope on the grounds that "there is no
domain here, only tooling". The four terms below are specific to this foundation's way of
working and are not general programming vocabulary, which is the test that convention applies.

The mutation measurement is re-taken here because tickets 01 to 04 add lines to the mutated
perimeter. The pinned minimum is a record of a measurement, verified at the time to
discriminate rather than merely to pass; a record that nobody re-takes decays silently,
and the more so because the mutation command sits outside the gate where nothing triggers it.
Well-tested additions should move the score up. If it moved down, that is a finding about the
new tests, and it is reported rather than accommodated — the pin is not lowered.

**Blocked by:** 01, 02, 03, 04

**Status:** resolved

- [x] ~~ADR-0003~~ **ADR-0005** records the placement rule and its two corollaries, in the format the existing decision records use — the number moved because the interface effort took 0003 and 0004 in the interval; see the first comment
- [x] It names trusted hosts as the only default in this effort taken on trust, and says what makes it so
- [x] It records why the reference package was refused as a dependency, and why automatic eager loading and aggressive prefetching were refused by name
- [x] ~~The project glossary is created at the repository root~~ **The four terms are appended to the glossary the interface effort created** at the repository root — default, guard, provable guard, default taken on trust — each defined in one or two sentences
- [x] The glossary carries no implementation detail: no call names, no paths, no environment conditions
- [x] The supersession of the quality gate spec's position on a glossary is stated where a reader of either document will meet it
- [x] The agent instruction index is verified to already point at both the glossary and the decision records; if it does not, it is corrected
- [x] The mutation score is re-measured, the new figure recorded, and the pinned minimum updated only if the score moved up
- [x] If the score moved down, the fall is reported with the escaping mutants named, and the pin is left where it is
- [x] The spec's status is moved to resolved
- [x] `composer test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

**The ADR is 0005, not 0003, and the glossary was appended rather than created.** Between the
writing of this ticket and its implementation, the home-redesign effort landed and took both
numbers this ticket assumed were free: ADR-0003 records that Livewire pages are classes and UI
primitives are templates, ADR-0004 that contrast outranks fidelity to the published theme. It
also created `CONTEXT.md` at the repository root with four interface terms — Token, Primitive,
Chrome, Page — reversing the same refusal this ticket set out to reverse, and saying so in its
commit message but in neither document. Nothing here needed re-deciding: the ADR is the same
record under a different number, and the four terms are appended to four that were already
there. The spec's two references to ADR-0003 were corrected, and its claim that the glossary
"does not yet exist and is created here" was corrected to what actually happened.

**The supersession is now stated in both documents rather than in a commit message.** The
quality gate spec's out-of-scope entry keeps its original reasoning and carries a dated
supersession note beneath it; `CONTEXT.md` carries the reciprocal line at its foot. The note
distinguishes what was refused from what exists: the refusal was of a glossary of the tooling,
and that glossary was never written. What exists names distinctions specific to this
foundation, which is the test the domain-documentation convention applies.

**The agent instruction index needed no correction.** `CLAUDE.md` already points at both under
"Domain docs" — `CONTEXT.md` plus `docs/adr/` — through `docs/agents/domain.md`, which
instructs skills to read the glossary and the relevant decision records before exploring. The
checkbox is verified rather than actioned.

**The mutation score did not move: 75.00 per cent, 21 tested of 28, unchanged.** The pin stays
at 75, and this is not the "moved down" branch — it is the "did not move" case the ticket did
not anticipate, and no pin is touched either way. The re-measurement this ticket asked for had
in substance already been taken: the home redesign re-measured after tickets 01 to 04 had
landed, found the score had risen from 72.00 over 25 to 75.00 over 28 on the strength of *this*
effort's tests, and raised the pin then. Nothing has entered `app/` since. Checked for
discriminating power the way the earlier figures were, rather than watched to pass: `--min=76`
exits one, `--min=75` exits zero.

The seven that escape are the same seven, and none of them belongs to this effort: two removals
of a method call in the provider, `Date::use` and `DB::prohibitDestructiveCommands`, both
predating this work; five in the shipped `User` model, in `initials()` and `casts()`. The three
mutants this effort added, on `Model::shouldBeStrict` and `URL::forceHttps`, are all killed.

**The gate needed a worktree setup before it could be believed.** Three failures on the way
were the environment rather than the code, and are recorded so the next agent in a fresh
worktree does not read them as findings. Rector reported thirty-six phar errors naming a
worktree that no longer exists — PHPStan's cache under the system temporary directory is shared
across checkouts, and deleting it cleared them. Pest could not measure coverage, because
`xdebug.mode` is `develop` locally where continuous integration uses pcov; `XDEBUG_MODE=coverage`
is the local equivalent. The home-page test then failed on a missing Vite manifest, because
`composer setup` — which migrates and builds — had not been run in this worktree. `composer test`
passes: 27 tests, 102 assertions, 100 per cent coverage, every analyser clean.
