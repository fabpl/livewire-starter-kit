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

**Status:** ready-for-agent

- [ ] ADR-0003 records the placement rule and its two corollaries, in the format the existing decision records use
- [ ] It names trusted hosts as the only default in this effort taken on trust, and says what makes it so
- [ ] It records why the reference package was refused as a dependency, and why automatic eager loading and aggressive prefetching were refused by name
- [ ] The project glossary is created at the repository root with the four terms — default, guard, provable guard, default taken on trust — each defined in one or two sentences
- [ ] The glossary carries no implementation detail: no call names, no paths, no environment conditions
- [ ] The supersession of the quality gate spec's position on a glossary is stated where a reader of either document will meet it
- [ ] The agent instruction index is verified to already point at both the glossary and the decision records; if it does not, it is corrected
- [ ] The mutation score is re-measured, the new figure recorded, and the pinned minimum updated only if the score moved up
- [ ] If the score moved down, the fall is reported with the escaping mutants named, and the pin is left where it is
- [ ] The spec's status is moved to resolved
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
