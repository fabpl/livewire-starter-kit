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

**Status:** ready-for-agent

- [ ] The aggregate command enforces a hundred per cent minimum
- [ ] The measured perimeter is the application namespace and nothing wider
- [ ] Both branches of the model's initials helper are exercised
- [ ] The production branch of the password-defaults closure is exercised
- [ ] The pipeline installs a coverage driver
- [ ] Full coverage is reached with no suppression, no lowered threshold and no widened perimeter
- [ ] The tests written assert observable results rather than how they are produced
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
