# 04 — Install the suite's own defaults in the suite

**What to build:** the test suite refuses, by construction, to do two things it should never do.
An outbound HTTP call that was not faked fails the test instead of reaching a third party, and
a sleep is recorded rather than served.

Both belong to the suite and are installed by the test bootstrap — not by the application
service provider, which is where the reference package puts them because a dependency cannot
reach anyone's test bootstrap. This repository owns its own, and the difference is the point of
the ticket rather than a detail of it.

The package guards both on "am I running under test", which is always true while the suite
runs. A mutant that removes that condition and makes the call unconditional cannot be killed by
any test, because no test can run outside the suite to observe the difference. That would put a
condition inside the measured perimeter that this repository's own gate is structurally unable
to prove. Installing them in the test bootstrap removes the condition instead of covering it:
the code is reachable only under test, so there is no guard to write and nothing to prove about
one.

The stray-request prohibition earns its place on its own merits — an unfaked call is slow,
intermittent, and a disclosure to a third party. Faking sleep earns its place through the gate
rather than the suite: the mutation command replays the suite once per mutant, so a second of
real waiting becomes minutes, and the symptom presents as "the gate is slow" rather than as
"someone wrote a sleep".

Their known asymmetry is worth carrying into the code review: the stray-request prohibition
raises, while the faked sleep substitutes silently, so a test purporting to verify a delay
would pass vacuously. The framework's assertion on recorded sleeps is the remedy, and it has to
be reached for deliberately.

Both are asserted, and that is the part most likely to be dismissed as unnecessary. These
settings live where nothing watches them: delete the hook and no test fails, and the protection
disappears in silence. That is precisely the class of drift this repository is built against.
Two ordinary assertions close it, at no new seam.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] Both defaults are installed by the test bootstrap, per test, and neither is installed by the application service provider
- [ ] Neither carries a "running under test" condition
- [ ] Both are scoped to the unit and feature suites; the browser suite is left with its own notion of waiting and its own network
- [ ] An outbound HTTP call that was not faked fails a test, and a test asserts it
- [ ] A sleep is recorded rather than served, and a test asserts it
- [ ] The suite's wall-clock time does not increase measurably
- [ ] Full coverage of the application namespace still holds, with no suppression and no lowered threshold
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
