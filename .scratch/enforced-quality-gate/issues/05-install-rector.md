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

**Status:** ready-for-agent

- [ ] Rector and its Laravel rule package are development dependencies
- [ ] Exactly the agreed rule groups are enabled and no others
- [ ] The coding-style, naming, docblock-type, named-argument and test-framework groups are absent
- [ ] The language target and the framework version are both resolved from the dependency manifest rather than pinned by hand
- [ ] Rector's perimeter matches the tree the rest of the chain covers
- [ ] A fix command applies rewrites and a check command reports without writing
- [ ] The aggregate command runs the check form and fails on a tree Rector would change
- [ ] The whole tree is rewritten and committed
- [ ] Running Rector a second time produces no further change
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
