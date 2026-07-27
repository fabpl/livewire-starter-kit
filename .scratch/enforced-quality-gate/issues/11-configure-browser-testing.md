# 11 — Configure browser testing as a diagnostic command

**What to build:** a developer can ask whether the application actually works in a browser —
that the page loads, that nothing lands in the browser console, and that the navigation paths a
user takes succeed. One command drives a real browser through those scenarios, available on
demand and, like mutation, deliberately **outside** the blocking gate.

Every check the chain holds today observes the code without ever running the page. Static
analysis reads types, the architecture rules read namespaces, the feature suite asserts an HTTP
response. None of them sees a template that renders and then throws once the browser executes
it, a script that fails to boot, or an asset the build did not emit. That defect class is
invisible to the entire gate and visible to the first person who opens the application.

The console assertion comes first because it is the cheapest and the most easily lost. A page
whose console is full of errors still returns a successful response and still satisfies every
assertion the feature suite makes; treating console output as a failure is what separates "the
page rendered" from "the page works". The navigation scenarios come second and assert success
paths only — the application has one route today, so their value is a harness that is ready when
there are ten, not the coverage they provide now.

These tests are excluded from the coverage measurement, deliberately and for the same reason
mutation sits outside the gate. A line traversed by a browser scenario would count as covered,
and the hundred per cent minimum would then be satisfiable by driving pages rather than by
asserting behaviour — which turns ticket 09's requirement into a formality. The browser suite
therefore runs as its own pass, and the pass that measures coverage never sees it.

Keeping it out of the aggregate command is the maintainer's decision and its cost is stated
rather than hidden: nothing triggers this command, so it is an instrument that will one day fail
for reasons nobody tracked — exactly the consequence ticket 10 accepts. What is bought in
exchange is the pipeline's simplicity, since a browser suite inside the gate means installing a
browser binary on every continuous-integration run for an application that currently has a
single page. If the application grows a real interface, this is the first decision to revisit.

**Blocked by:** 04, 09

**Status:** ready-for-agent

- [ ] The Pest browser plugin is a development dependency
- [ ] The browser binaries it drives are installed by a documented command rather than assumed present
- [ ] Browser tests live in their own suite, separate from the unit and feature suites
- [ ] A dedicated command runs that suite and only that suite
- [ ] The command is **not** part of the aggregate command and is not run by the pipeline
- [ ] The aggregate command's test run does not execute the browser suite, so no browser scenario contributes to the coverage minimum
- [ ] Every browser test asserts that nothing was written to the browser console
- [ ] The success path of each navigation the application offers is exercised end to end
- [ ] Browser tests are held to the same standard as the rest of the tree: functional style, strict types, analysed and formatted
- [ ] No suppression, no architecture exemption and no lowered coverage threshold is introduced to accommodate the new suite
- [ ] No scheduled or periodic workflow is added
- [ ] `composer test` passes, and its runtime is unchanged
- [ ] Committed as a single commit following the repository's Conventional Commits convention
