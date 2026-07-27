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

**Status:** ready-for-agent

- [ ] The framework's own preset is kept and the agreed rules are added on top
- [ ] Class finality, return-type insertion and native function invocation are absent
- [ ] Name importing is limited to classes
- [ ] The whole tree is reformatted and committed
- [ ] Running Pint a second time produces no further change
- [ ] Running Rector and then Pint produces no further change — the chain has a fixed point
- [ ] No path was excluded from Pint in order to make the run pass
- [ ] A comment carrying real explanation can still be kept, using an escape prefix, and this is demonstrated
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
