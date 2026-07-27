# An enforced quality gate replaces fidelity to the upstream kit

The repository was installed as `laravel/blank-livewire-starter-kit`, unmodified, on the
principle that staying identical to upstream made it cheap to re-derive and diffable
against new kit releases. That principle is abandoned. In its place: the strictest
position each quality tool can express, applied without exception to the entire tree —
including the files the kit shipped — and enforced on every commit by a single aggregate
command.

The motive is who writes the code here. This repository is tooled for agents, and prose
instructions do not constrain them; a mechanical gate does. The moment to pay for it is
now, at 30 PHP files and 3 frontend files, rather than after the first real feature.

## Considered options

Two narrower positions were examined and rejected.

**Strictness on `app/` only, kit files frozen.** Not mechanically expressible: `pint.json`
carries a single rule set, and excluding a path removes it from the linter entirely rather
than relaxing it. `config/` would end up with no formatting at all — not "less strict
here" but "no safety net here", the opposite of the intent.

**Formatting everywhere, coverage only on newly authored code.** Rejected because the
boundary would become the gap agents fall through, and it would sit exactly where mistakes
are most expensive.

## Consequences

The tree is no longer byte-identical to the published kit. Upgrading the kit becomes a
manual merge rather than a diff, and "rebuild this repository" now reads "reinstall the
kit, **then** replay the quality chain" — still reproducible, because the chain is
deterministic, which is why the tool versions are pinned in the lockfile.

Four decisions of the foundation spec are reversed by this one: its refusals of Rector, of
a coverage threshold, of a higher static-analysis level, and of Pest. Its `Additions. None.`
clause no longer holds. What survives is its single seam — one command, identical locally
and in CI.
