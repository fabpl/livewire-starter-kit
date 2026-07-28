# 01 — Make models strict outside production

**What to build:** a developer who mistypes an attribute in a mass assignment finds out
immediately instead of wondering later why the field is empty, and a developer who reads a
column that was never loaded gets an error rather than `null`. In production the framework's
own permissive behaviour is restored, so a mistake that reached production degrades into
slowness rather than into a failed page.

Only one of the three effects has a security dimension, and it is the reason the environment
gate points this way rather than the other. An unloaded column reads as `null`, and `null`
reads as the absence of a restriction — a banned-at, a revoked-at, a role. That has to be
caught before production, which is where this repository catches things, per
[ADR-0001](../../../docs/adr/0001-enforced-quality-gate-over-upstream-fidelity.md).

This is the mirror of the two environment-gated rules the provider already carries: they
switch on in production, this one switches off.

Enabling strictness can surface violations in code that was already there, so the suite has to
be looked at rather than assumed. Checked in advance and expected to be clean: the only test
that constructs a model assigns a declared attribute, and nothing else in the suite touches a
model. If a violation does appear, it is a defect this ticket found, not an obstacle — fix the
code, never the gate.

The depth of proof is settled and deliberately shallow. What this repository decides is *which
environments*, and that is what the test pins, together with one of the three effects — the
one that costs neither a database nor a relationship. The other two are taken on the
framework's word. In particular the best-known of them, the prohibition on lazy loading, is
asserted by nothing here, because asserting it would mean adding a relationship to the
foundation's single model purely so that a test can violate it. That addition is refused.

**Blocked by:** None — can start immediately.

**Status:** resolved

- [x] Models are strict in every environment except production
- [x] Assigning an attribute the model does not declare raises, and a test asserts it
- [x] A test asserts that the same assignment is tolerated once the application runs in production, following the two-case shape of the existing password-defaults test
- [x] No relationship is added to any model in order to make something testable
- [x] The existing suite passes without any test being loosened to accommodate strictness; if one needed changing, the change is a correction and is recorded in the comments below
- [x] Full coverage of the application namespace still holds, with no suppression and no lowered threshold
- [x] `composer test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

The suite was clean, as the ticket predicted: no existing test needed changing, so there is no
correction to record. `Model::shouldBeStrict(! app()->isProduction())` in the provider and
`tests/Feature/StrictModelsTest.php` are the whole change.

No comment was written at the call site. The point-of-call exception the spec's placement rule
describes belongs to a default no level makes provable, and this one is provable — the two
cases the test carries are what state the decision. The two environment-gated rules the call
sits between carry no comment either. The reasoning for the direction of the gate stays in the
spec, and ADR-0003 records the rule.

The test file does carry a note, for the opposite reason: it records what is deliberately *not*
asserted, which nothing in the code can show.

Mutation was run beyond the gate to confirm the two cases earn their keep. Both mutants on the
new call are killed — dropping the negation fails the first case, removing the condition fails
the second — and the score stays above the `mutate` script's minimum.
