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

**Status:** resolved

- [x] Both defaults are installed by the test bootstrap, per test, and neither is installed by the application service provider
- [x] Neither carries a "running under test" condition
- [x] Both are scoped to the unit and feature suites; the browser suite is left with its own notion of waiting and its own network
- [x] An outbound HTTP call that was not faked fails a test, and a test asserts it
- [x] A sleep is recorded rather than served, and a test asserts it
- [x] The suite's wall-clock time does not increase measurably
- [x] Full coverage of the application namespace still holds, with no suppression and no lowered threshold
- [x] `composer test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

Both live in `tests/Pest.php`, in one `beforeEach` scoped `->in('Feature', 'Unit')`. The
application service provider is untouched, and neither call asks whether it is running under
test — there is nowhere left for that question to be asked.

**The scope is asserted rather than declared.** The two tests sit in different suites on
purpose — the prohibition in `tests/Feature/ProhibitedStrayRequestsTest.php`, the faked sleep
in `tests/Unit/FakedSleepTest.php` — so that dropping either name from the scope fails a test
instead of passing in silence. Both directions were verified by removing one name at a time and
watching the corresponding test fail. What this does not buy is the other half of the matrix:
nothing asserts that the sleep is faked under `Feature` or that stray requests are prohibited
under `Unit`. Closing that would take four tests where the ticket budgets two, and the failure
it would catch — Pest's directory scoping applying to one listed directory but not another —
is not a failure this bootstrap can produce.

**The browser suite's exclusion is by construction, not by measurement.** The browser plugin
scopes its own hooks with the same `->in()` mechanism, and that mechanism was shown to exclude
unlisted directories. Observing it directly would have meant installing Playwright, which the
suite this ticket touches does not need.

**The faked-sleep test asserts on the recorded sequence and nothing else.** A first draft also
measured elapsed time, and the review removed it as adding no kill power: the fake appends the
duration and returns, so recording and serving are mutually exclusive by construction and no
reachable state passes `Sleep::assertSequence` while having actually slept.

**Wall-clock.** The unit and feature suites went from 560 ms over ten tests to about 640 ms
over twelve. The added time is the two new tests booting, not the defaults, which cost nothing
per test and can only ever remove waiting.
