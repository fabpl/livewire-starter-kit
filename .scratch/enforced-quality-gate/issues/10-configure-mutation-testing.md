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

**Status:** ready-for-agent

- [ ] A dedicated command runs mutation testing over the covered code, in parallel
- [ ] The command is **not** part of the aggregate command and is not run by the pipeline
- [ ] Its minimum score is set from a measurement rather than guessed
- [ ] The measured score and the date of the measurement are recorded in the spec
- [ ] No scheduled or periodic workflow is added
- [ ] The aggregate command still passes, and its runtime is unchanged
- [ ] Committed as a single commit following the repository's Conventional Commits convention
