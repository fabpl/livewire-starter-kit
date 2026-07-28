# 09 — Require full coverage of the application code

**What to build:** untested application code stops being committable. Every line of the
application namespace is executed by a test, the requirement is enforced by the aggregate
command, and the pipeline gains the driver that measures it.

This is the only ticket in the effort that is writing work rather than configuration, and its
size is genuinely unknown in advance — which is why it is one ticket rather than a threshold
ticket and a tests ticket. Splitting it would leave the first half red until the second landed.

Two costs are known from reading the code rather than estimated. The model's initials helper
carries a ternary whose second branch a single test does not exercise. The service provider
registers a password-defaults closure whose production branch never runs in the testing
environment — and which is only invoked at all when a password validation occurs — so reaching
it means deliberately simulating production. Full coverage is cheap here, but it is not free.

The perimeter stays the application namespace and is not widened. Routes are declarations and
migrations are single-use scripts; demanding coverage there produces tests asserting that the
framework can route and migrate, which the foundation effort already identified as wasted work.
The escape that leaves open — logic hidden in a route closure, outside the measured perimeter —
is closed by ticket 08, which prohibits the logic rather than measuring it. The escape via
suppression annotations is closed by ticket 02.

**Blocked by:** 02, 04, 08

**Status:** resolved

- [x] The aggregate command enforces a hundred per cent minimum
- [x] The measured perimeter is the application namespace and nothing wider
- [x] Both branches of the model's initials helper are exercised
- [x] The production branch of the password-defaults closure is exercised
- [x] The pipeline installs a coverage driver
- [x] Full coverage is reached with no suppression, no lowered threshold and no widened perimeter
- [x] The tests written assert observable results rather than how they are produced
- [x] `composer test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

**The threshold is `--min=100` on the aggregate command's Pest invocation, and it was verified
to bite rather than assumed.** Removing the model's test file and re-running the command exits
one; restoring it exits zero. The perimeter is untouched — `phpunit.xml` already declared
`<source>` as `app` alone, which is what the ticket asked for, so there was nothing to narrow
and nothing to widen. The pipeline's `coverage:` key moves from `none` to `pcov`; that is the
whole of the workflow change.

**The first of the two known costs was named too narrowly, and the correction is the more
interesting half of this ticket.** The ticket predicted a ternary whose second branch one test
would miss. Measured, the model was not partly covered but entirely uncovered — zero of ten
elements, `casts()` as untested as `initials()` — because no shipped test ever constructs a
user. The whole application measured 39.1 per cent: eleven of twenty-eight elements. So the
ternary was real, and it was the smaller half of a gap the reading had not seen the shape of.
Two tests cover the model, one per branch: a three-word name reduces to its outermost initials,
a one-word name keeps its single one. Constructing the model covers `casts()` on the way, which
is a consequence rather than a target.

**The second cost was exactly as read.** Lines 36 to 41 of the provider — the production arm of
the password closure — were the only uncovered region of it, and the closure was never invoked
at all beforehand; the clover counts confirm the enclosing `Password::defaults(…)` call ran once
per boot while the arm ran zero times. Reaching it takes `detectEnvironment` and a fresh
`boot()`, then asking the framework for the default rule. The pair of tests asserts what the
rule accepts, not how it is built: the same eight-character password passes outside production
and fails inside it, and the failure names the twelve characters production demands.

**A network call sits one step past where the test stops, and it is stated rather than
guarded.** The production rule ends in `uncompromised()`, which queries an external service —
in a gate, a source of red that has nothing to do with the code, and an outbound hash of
whatever password the test offers. The test never reaches it: the rule's own length check fails
first and it returns before verifying.

**The first probe used to establish that was worthless, and the correction is worth more than
the finding.** Forbidding stray requests and watching the test still pass looks like evidence
and is not: `NotPwnedVerifier::search()` wraps its request in a `try`, reports the exception and
carries on with an empty body, so a blocked request is swallowed and the password is treated as
uncompromised. The probe would have passed whether or not a request was attempted, which makes
it a probe with no negative result. Faking the client and asserting nothing was sent is a real
one, and it was checked for discriminating power rather than just run: the same probe on a
twenty-two character password — long enough to clear the length check — records a request to
`api.pwnedpasswords.com`. On the eight-character password the suite actually offers, nothing is
sent. Both probes were then removed, because asserting the absence of a request would be
asserting how the rule works, which the spec's testing decisions rule out. The residual
exposure is recorded instead: raise the test's password past twelve characters and the gate
starts calling the network.

**What the threshold enforces is weaker than what these tests assert, and the gap is in the
model.** Line coverage counts a line traversed, and the ternary in `initials()` occupies one
line — so the multi-word test alone reports the model at a hundred per cent. The one-word test
is required by the ticket and not by the gate; delete it and nothing goes red. That is not a
defect in the threshold, which the spec deliberately scopes to lines with branch coverage out
of scope, but it does mean the "both branches" box is held by the tests existing rather than by
anything mechanical. Mutation testing is the check that would hold it, and it arrives in ticket
10 outside the blocking gate — so the honest statement is that this branch is covered by
convention.

**`composer test` gains its first prerequisite beyond an install.** The threshold cannot be
evaluated with no coverage driver, and Pest's message for that condition — coverage could not
be obtained — looks like a failing check while being nothing of the kind. An agent meeting it
with the quality-gate skill in hand and no note about drivers has a lowered `--min` within easy
reach, which is exactly the repair that skill exists to forbid. It now names the condition and
the fix. That is the only file changed here that the ticket did not ask for, and the reason is
that the ticket installed the failure mode.

**Outside this ticket, recorded because it recurs.** The chain's first run in a fresh worktree
failed inside Rector with a phar path belonging to a worktree that no longer exists. Rector's
own cache is per-checkout, as is Larastan's, but the PHPStan instance Rector embeds caches under
the system temporary directory where every checkout on the machine shares it. Clearing that
directory fixes it. This is an environment fault rather than a code one, and closing it properly
would mean pinning that cache per checkout the way `phpstan.neon` and `rector.php` already do —
a change to the chain's configuration, and so a decision for a human rather than one to take on
a coverage ticket.
