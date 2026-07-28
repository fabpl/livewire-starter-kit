# 10 — Configure mutation testing as a diagnostic command

**What to build:** a developer can ask whether their tests actually *detect* changes, not
merely execute lines. One command mutates the code and reports how much of it the suite
notices — available on demand, and deliberately **outside** the blocking gate.

The reasoning matters more than the configuration, because the conclusion looks like a retreat
and is not. Full line coverage guarantees that every line was *traversed*, never that it was
*tested* — a test with no assertions covers perfectly. Mutation is the check that closes that
gap. It is also the only check in the chain whose cost grows quadratically, as mutants
multiplied by suite runtime, with both factors growing as the project does.

Keeping it out of the aggregate command preserves the property the whole chain rests on: the
local command and the pipeline command are the same one. Letting the pipeline run a weaker set
would produce divergence in its most confusing direction — a developer blocked locally while
the pipeline stays green.

The consequence is accepted rather than hidden: nothing enforces the mutation score. It is an
instrument, and an instrument that nothing triggers will one day fail for reasons nobody
tracked. A minimum is retained so the command self-evaluates when run. The mutation-ignore
annotation stays forbidden, and that prohibition rides on ticket 02, which *is* in the gate.

A scheduled job was considered and rejected: a red that nobody reads erodes the credibility of
the gates that do matter, and this repository has neither a remote nor an audience for such a
notification.

**Blocked by:** 02, 09

**Status:** resolved

- [x] A dedicated command runs mutation testing over the covered code, in parallel
- [x] The command is **not** part of the aggregate command and is not run by the pipeline
- [x] Its minimum score is set from a measurement rather than guessed
- [x] The measured score and the date of the measurement are recorded in the spec
- [x] No scheduled or periodic workflow is added
- [x] The aggregate command still passes, and its runtime is unchanged
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

**The command is `composer mutate`, and it declares no perimeter.** Pest takes the paths to
mutate from `phpunit.xml`'s `<source>` when the command line names none, so the mutated
perimeter is the measured perimeter by construction rather than by a second declaration free to
drift from the first. `--everything` reads like a widening and is not one: it means "filter by
no class list", and it is what stands in for the `covers()` annotations this repository does not
write. Without it — or a `--path`, or a `mutates()` call — the plugin refuses to run at all
rather than defaulting to something.

**72.00 per cent, measured on 28 July 2026 over 25 mutants in the two application files, and
`--min=72` is that number rather than a target.** It was checked for discriminating power
instead of being watched to pass: `--min=73` exits one, `--min=72` exits zero. Parallel and
serial runs return the same score and the same mutant count, so the figure is a measurement and
not a sample of one.

**`--covered-only` is not the no-op it should have been, and the finding is written up in the
spec rather than restated here.** The short version for anyone reading the ticket alone: five
mutants land on the model's `#[Fillable]` and `#[Hidden]` attribute lines, an attribute is not
an executable statement so no driver ever marks it traversed, and the flag therefore excludes
them even at a hundred per cent coverage — 72.00 per cent with it, 60.00 without. Kept, with
the blind spot recorded rather than enjoyed.

**The seven escaping mutants are listed in the spec and repaired nowhere, because raising the
score is writing tests and this ticket is configuration.** Worth noting here only for what it
settles: two of them are the `initials()` ternary that ticket 09 closed by writing a test the
gate does not require, and recorded as "covered by convention" for exactly that reason. It is
no longer convention — an instrument now names that branch when the assertion goes missing.

**The aggregate command is untouched and so is the workflow.** No scheduled job, no new
pipeline step, no change to `test` or `ci:check`; `composer test` runs the same seven steps in
the same order, which is the point of keeping mutation beside the gate rather than in it. The
new script does borrow one line from it — `config:clear` — because a stale cached configuration
would quietly change what the instrument measures, and a number produced from a configuration
nobody chose is worse than no number.

**One document changed that the ticket did not name: the quality-gate skill.** It already
forbade lowering "a mutation score" and already refused `@pest-mutate-ignore`, for a command
that did not exist — so this makes an existing sentence true rather than adding a rule. The
section added is short and states no reasoning, because that file says of itself that the
configuration is normative and the reasoning belongs in the spec.

**Outside this ticket, and the second recurrence of a fault already recorded.** The chain's
first run in this worktree failed inside Rector with a phar path belonging to a deleted
worktree, exactly as ticket 09 described. The location is worth pinning down, since it was
looked for in the wrong place first: the shared cache is `$TMPDIR/cache/PHPStan`, not
`$TMPDIR/phpstan`. Removing that directory restores the chain. Still an environment fault
rather than a code one, and still a decision for a human rather than one to take on a mutation
ticket.
