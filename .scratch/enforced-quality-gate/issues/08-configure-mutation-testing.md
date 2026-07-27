# 08 — Configure mutation testing as a diagnostic command

**What to build:** a `composer test:mutate` command that measures whether the tests actually
detect changes, available on demand and deliberately **outside** the blocking gate.

The reasoning matters more than the configuration here. Full line coverage guarantees that
every line was *traversed*, never that it was *tested* — a test with no assertions covers
perfectly. Mutation is the check that closes that gap. It is also the only check in the chain
whose cost grows quadratically, as mutants multiplied by suite runtime with both factors
growing with the project, which is why it stays out of `composer test`.

Keeping it out preserves the property the whole chain is built on: the local command and the
CI command are the same one. Letting CI run a weaker set would block a developer locally while
the pipeline stayed green — the divergence in its most confusing direction.

The consequence is accepted rather than hidden: nothing enforces the score. It is an
instrument, and an instrument nothing triggers will one day fail for reasons nobody tracked.
`--min` is kept so the command self-evaluates when it is run. `@pest-mutate-ignore` stays
forbidden, and that prohibition rides on ticket `07`'s check, which is in the gate.

**Blocked by:** 07

**Status:** ready-for-agent

- [ ] `composer test:mutate` runs `pest --mutate --covered-only --parallel`
- [ ] The command is **not** part of `composer test` and is not run by CI
- [ ] `--min` is set from a measured score rather than guessed
- [ ] The measured score and the date of the measurement are recorded in the spec
- [ ] No scheduled or nightly workflow is added
- [ ] `composer test` still passes and its runtime is unchanged
- [ ] Committed as a single commit following the repository's Conventional Commits convention
