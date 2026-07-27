# 02 — Forbid the suppression annotations

**What to build:** the two annotations that would let anyone exempt code from coverage or
from mutation stop working. Adding either one anywhere in the tree fails the quality gate,
locally and in the pipeline alike.

Nothing in the toolchain watches for them — not the formatter, not the analyser, not the
architecture rules. Without a dedicated check, the prohibition recorded in the spec is only
an intention, and the first agent facing a red coverage run will discover it as such.

This lands **before** the gates it guards, and the order is the point. In the reverse order
there is a window during which full coverage is demanded while the escape from it is still
available — precisely the moment a stuck agent would find it.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] A check fails when a coverage-ignore or mutation-ignore annotation appears anywhere in the tree
- [ ] The check is its own command, runnable on its own as well as from the aggregate command
- [ ] The aggregate command runs it, so the pipeline inherits it without a workflow change
- [ ] Adding one of the annotations is demonstrated to fail the gate, and removing it to restore green
- [ ] The check reports which file and line offends, rather than failing silently
- [ ] Vendored and generated directories are outside its reach, so a dependency cannot fail the build
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
