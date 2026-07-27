# Spec: enforced quality gate

Status: ready-for-agent

## Problem Statement

A developer opening this repository cannot tell what standard their work will be held to,
because the standard barely exists. The quality gate is the one the starter kit ships:
formatting on its default preset, static analysis at a middling level, a test runner with
no coverage requirement. It passes on the current tree — but so would almost anything.
Measured before writing this spec, static analysis at its *maximum* level also reports
zero errors, which says less about the code than about how little is being asked.

That gap matters because of who writes the code here. The repository is tooled for agents:
a commit convention, an issue-tracker convention, domain-documentation rules, a skills
directory. Agents drift stylistically, comment redundantly, leave branches untested, and
when a check fails they reach for the nearest suppression rather than the fix. None of that
is caught by prose in an instruction file. Only a mechanical gate catches it.

There is also a deadline that nobody set but which is real. The repository holds 30 PHP
files — 28 of them shipped by the kit — three frontend files and two example tests. A
reformatting decision taken today costs a diff nobody needs to read. The same decision taken
after the first real feature costs a migration, and after the tenth it will simply not be
taken.

## Solution

One command tells a developer whether their work is acceptable, and it is the same command
continuous integration runs.

Behind it sits a chain of five tools — Rector, Pint, Larastan, Pest and Prettier — each
holding the strictest position it can express, applied to the whole tree without exception.
Structural rewriting, syntax formatting, static analysis, behaviour and architecture
testing, and frontend formatting. Every tool owns one
concern and no concern has two owners. The command that fixes and the command that checks are
distinct, and the tree that gets committed is one neither of them would change.

The gate offers no way out. There is no suppression annotation, no analysis baseline, no
per-file waiver, no threshold to lower. A check that fails is a thing to fix, and where an
agent genuinely disagrees with a rule the escalation is to a human changing the configuration
deliberately — never to routing around it.

This follows [ADR-0001](../../docs/adr/0001-enforced-quality-gate-over-upstream-fidelity.md),
which abandoned fidelity to the upstream kit in favour of an enforced gate, and
[ADR-0002](../../docs/adr/0002-pest-replaces-phpunit.md), which replaced the test runner
because the gate needs capabilities the previous one does not have.

## User Stories

1. As a developer, I want a single command that runs every quality check, so that I never have to wonder whether my change is acceptable.
2. As a developer, I want that command to be exactly what continuous integration runs, so that a green local run cannot become a red pipeline.
3. As a developer, I want a separate single command that fixes everything fixable, so that mechanical problems never reach a reviewer.
4. As a developer, I want the committed tree to be a fixed point of the whole chain, so that re-running the tools produces no further change.
5. As a developer, I want the fixing tools to run in a defined order, so that two rewriters never undo each other's work.
6. As a developer, I want the cheapest checks to run first, so that a formatting mistake does not cost me a full coverage run.
7. As a developer, I want strict typing declared in every file, so that silent coercion is never the cause of a defect here.
8. As a developer, I want loose comparisons rejected, so that type juggling cannot hide in a conditional.
9. As a developer, I want import order decided mechanically, so that it is never discussed in a review.
10. As a developer, I want redundant commentary removed automatically, so that a comment surviving in this tree is worth reading.
11. As a developer, I want a marked way to keep a comment that carries real explanation, so that the rule removes noise rather than knowledge.
12. As a developer, I want static analysis at the strictest setting it can express, so that type defects surface before runtime.
13. As a developer, I want analysis to cover my tests as well as my application code, so that the largest body of code in the project is not exempt.
14. As a developer, I want analysis pinned to the language version the pipeline runs, so that syntax my machine accepts cannot break the pipeline.
15. As a developer, I want the language floor declared once and honoured everywhere, so that several files cannot disagree about which version this is.
16. As a developer, I want architectural rules verified by the test suite, so that structural drift fails visibly instead of accumulating quietly.
17. As a developer, I want every line of application code executed by a test, so that untested code cannot be committed.
18. As a developer, I want business logic kept out of route declarations, so that the measured perimeter cannot be sidestepped.
19. As a developer, I want a way to check whether my tests actually detect changes, so that a coverage percentage does not become a number I trust blindly.
20. As a developer, I want my templates, styles, scripts, workflow files and documentation formatted mechanically, so that the frontend is held to the same standard as the backend.
21. As a developer, I want utility classes sorted automatically in my templates, so that class order is never a matter of opinion.
22. As a developer, I want the formatter for templates and the formatter for PHP to have an enforced boundary, so that they cannot fight over the same file.
23. As a maintainer, I want each tool to own its concern alone, so that the chain converges instead of oscillating.
24. As a maintainer, I want fixers and verifiers separated, so that a broken rule is documented by its failure rather than repaired in silence.
25. As a maintainer, I want machine-owned manifests and lockfiles left to their owners, so that installing a dependency does not turn the pipeline red.
26. As a maintainer, I want tool thresholds pinned rather than aliased, so that upgrading a tool cannot raise the bar without a commit.
27. As a maintainer, I want no suppression mechanism available anywhere, so that a failing check is fixed rather than filed away.
28. As a maintainer, I want every tool version pinned in a lockfile, so that the chain is reproducible on a fresh machine.
29. As a maintainer, I want each step of the rollout to leave the gate green, so that the default branch is never broken mid-effort.
30. As an agent working in this repository, I want the standard I am held to expressed as executable configuration, so that I can verify my own work before reporting it done.
31. As an agent working in this repository, I want to be told which repairs are forbidden when a check fails, so that I fix the code instead of weakening the gate.
32. As an agent working in this repository, I want a way to raise a disagreement with a rule, so that my only options are not "comply" and "circumvent".
33. As a future contributor, I want the departure from the upstream kit recorded as a decision, so that I do not mistake it for neglect.
34. As a future contributor, I want the reversal on the test runner recorded as a decision, so that I do not treat it as an accident.
35. As a future contributor, I want the measurements behind each choice preserved, so that reopening a decision starts from evidence rather than from taste.

## Implementation Decisions

### Scope of enforcement

Everything, without exception, including the files the kit shipped. Measured beforehand:
strict-type declarations alone reach 26 of the 30 PHP files.

Two narrower positions were examined and rejected. Confining strictness to the application
namespace is not mechanically expressible — Pint carries a single rule set, and excluding
a path removes it from Pint entirely rather than relaxing it, so
configuration files would end up with no formatting at all. Confining only the coverage
requirement was rejected because the boundary would become the gap agents fall through, and
it would sit precisely where mistakes are most expensive.

The accepted cost, per ADR-0001: the tree is no longer identical to the published kit, so a
kit upgrade becomes a manual merge rather than a diff, and "rebuild this repository" now
reads "reinstall the kit, then replay the chain".

### Ownership of concerns

Exactly one fixer per concern, plus an independent verifier wherever one exists. Verifiers
never fix. This is not tidiness: two fixers on one concern can disagree indefinitely, which
would make the aggregate command non-terminating — a failure that is silent and unpleasant
to diagnose.

| Concern | Fixer | Verifier |
| --- | --- | --- |
| Strict-type declarations | Pint | Pest architecture tests |
| Strict equality | Pint | Pest architecture tests |
| Import order and name importing | Pint | — |
| Comment and docblock hygiene | Pint | — |
| Class finality | Rector | Pest architecture tests |
| Dead code and type declarations | Rector | Larastan |
| Templates, styles, scripts, workflows, documentation | Prettier | — |
| Dependency manifests and lockfiles | Composer, npm | — |

Class finality belongs to Rector and explicitly not to Pint. The reason is
technical rather than aesthetic: Pint's rule is purely syntactic and would finalise
the abstract base test case, which exists to be extended. Rector performs inheritance
analysis before deciding. Only one of the two is correct.

### Chain order

Fixing runs Rector, then Pint, then Prettier. Rector emits code in its
own formatting and Pint normalises afterwards; the reverse order leaves the tree
dirty.

Checking carries no such constraint — each tool observes a frozen tree independently — so the
check chain is ordered by increasing cost instead. A formatting failure must not cost a
coverage run.

### Language floor

Raised to PHP 8.5 from 8.3. This closes a real gap rather than chasing a version: the
dependency constraint said 8.3, the pipeline installed 8.3, and the development machine runs
8.5, so 8.4 syntax would pass locally and fail remotely with nothing in between to catch it.
After the change the dependency constraint, the workflow, Larastan's assumed version and
Rector's target all state the same thing.

Verified in advance: Rector ships a rule set for 8.5, the CI action supports 8.5, and
the framework's own constraint is satisfied by it.

### Formatting — Pint

The framework's own Laravel preset is kept and rules are added on top. Swapping presets was measured
and rejected: the three alternatives are not stricter, they *disagree* — one imposes
snake-case test methods and spaced concatenation, and the interoperability presets are floors
that the framework's preset already exceeds. A swap buys churn instead of rigour and would
break the principle that framework conventions here are the standard ones.

Rules added cover strict-type declarations, strict comparison and strict parameters, import
ordering, importing of globally-qualified class names, class-element ordering, superfluous
annotation removal, nullable-type normalisation, useless control-flow removal, string and
heredoc consistency, and immutable date construction.

Three exclusions are load-bearing and must not be quietly reinstated. Class finality and
return-type insertion belong to Rector. Native function invocation is excluded because
it wants a leading separator on global functions exactly where name importing wants an import
statement — the two are in direct conflict, and the micro-optimisation does not justify the
noise. Name importing is therefore limited to classes.

### Comments — Pint

Pint's annotation-only rule does not tidy docblocks; it forbids comments. Any block
or line comment carrying no annotation is deleted unless marked with one of three escape
prefixes. Measured on the current tree, it removes prose docblocks whose entire content
restates the method name, and a commented-out import, while preserving every typed
annotation.

This is the rule best aimed at the stated problem, because filler commentary is what agents
produce in volume and nothing else in the chain sees it. The escape prefixes are the point
rather than a loophole: they turn a comment into a deliberate act that shows up in a diff,
instead of a reflex. Configuration files are exempt by the rule's own design, since
configuration relies on commentary to document itself.

### Structural rewriting — Rector

Rector's PHP target and framework version are both resolved from the dependency
manifest rather than pinned by constant. Enabled rule groups: dead code, code quality, type
declarations, privatisation, early return, conditional simplification, instance checks, the
Rector's own recommended set, and the Carbon set. Privatisation is a synergy rather
than a conflict — it pushes protected members toward private, which is the direction the
architecture rules demand.

Disabled deliberately: the coding-style group, which overlaps Pint frontally. The
PHPUnit groups, which target test classes that will not exist. The groups for other
frameworks, out of stack. A deprecated group the tool itself warns about. Named-argument
conversion, which is large churn for no benefit.

Two exclusions deserve their reasoning recorded. The naming group is the only group in the
entire chain that rewrites *identifiers* — everything else edits syntax or structure, and a
name is where human intent is least guessable by a heuristic and least verifiable afterwards.
The docblock type group would make Rector a second docblock writer alongside
Pint, which is exactly the oscillation the ownership rule exists to prevent.

Automatic version detection here sits alongside a pinned analysis level, and that is
consistent rather than contradictory: an alias moves when the *tool* updates, unrelated to the
code, whereas version detection moves when *the framework* is upgraded — which is precisely
when rewriting is wanted.

### Static analysis — Larastan

The level is pinned as an integer at the current maximum rather than written as the alias, so
that a release adding a level cannot raise the bar on a dependency update nobody connected to
it. Raising the bar should be a commit. Measured: the maximum level already passes with zero
errors, so the raise itself is free today.

Two official extensions — phpstan-strict-rules and phpstan-deprecation-rules — are added
because they live *beyond* the maximum level and no level
contains them — they are the part that bites. The analysed perimeter widens to tests, the
public entry point and the whole bootstrap directory; measured cost is one error, in an
example test this effort deletes anyway.

No baseline. The project starts at zero errors so there would be nothing to record, and
installing the mechanism would only give agents somewhere to file future failures.

One cost in this spec is *not* measured, and it is flagged rather than buried: the noise
phpstan-strict-rules produces on idiomatic Laravel code could not be evaluated before
installation. If the result is unreasonable, phpstan-strict-rules is removed and the removal
recorded — never kept with its findings suppressed.

### Test runner and style — Pest

PHPUnit is replaced by Pest per ADR-0002, and tests are written in the functional style
exclusively. No test classes in the tree apart from the base case the suites bind to.

A hybrid policy — functional by default, classes where fixtures are heavy — was rejected most
firmly of all the options considered. It does not institutionalise flexibility; it
institutionalises a per-file judgement call that no tool can adjudicate, which is the exact
opposite of a goal that is about removing choices from agents.

The functional style also removes a conflict rather than creating one. The strict architecture
rules require every class to be final and non-abstract, while a base test case is abstract by
necessity; with no test classes, there is nothing to exempt.

One defect is corrected on the way. A shipped example test extends the bare test case while
importing the database-refresh trait; without an application there is no trait setup, so the
trait does nothing. It is decoration that misstates what the test does, and it disappears in
the rewrite rather than being carried across.

### Architecture rules — Pest

Four of Pest's presets: strict, language hygiene, security, and Laravel conventions. No exemptions
anywhere.

The strict set's source was read rather than its documentation, because the two differ in
consequence. It requires no protected methods, no abstract classes, strict types, strict
equality, final classes, and no sleeping. Its collisions with the current tree are resolved by
changing the code: an abstract empty controller base that nothing extends is deleted, a
protected helper becomes private, and a model's cast declaration becomes public — necessarily
public rather than private, because the framework declares it protected and the language
forbids narrowing visibility on override.

One reservation is recorded in advance, because it is the rule most likely to be regretted.
The prohibition on protected methods conflicts with the framework's design rather than with
this code: framework extension points are protected by convention, so each override must be
exposed publicly, a visibility that misstates the intent. Today this costs two keywords. The
recorded fallback, if it becomes intolerable, is to write the individual expectations by hand
minus that one — rule sets cannot be cherry-picked, since exemptions apply to namespaces and
never to individual expectations. That is a decision to raise with a human, not to take in
passing.

### Frontend formatting — Prettier

Three Prettier plugins, and the combination was settled by measurement rather than reputation. The
better-known template plugin formats templates but leaves utility classes **unsorted**,
because the Tailwind sorting plugin monopolises an API and carries explicit workarounds for a fixed
list of plugins that includes no template plugin at all. The chosen template plugin solves it
differently, by declaring the Tailwind plugin as a peer dependency, and sorting then works.
The third, a PHP plugin, is not an addition — npm will demand it as a peer dependency
of the first. The chosen plugin also has the larger user base.

Two configuration details are not optional. Quote style must be set explicitly, or the plugin
rewrites single-quoted calls inside templates to double quotes, disagreeing with Pint.
Indentation must be set explicitly, since the plugin's default contradicts the
editor configuration.

Prettier owns templates, styles, scripts, workflow files and documentation. It
does not own dependency manifests or lockfiles: those already have owners that rewrite them on
every install. The measured disagreement there is a single missing final newline — trivial in
itself, and exactly the kind of recurring unrelated failure that gets an entire gate switched
off after three months.

Documentation is included deliberately. The issue tracker holds agent-authored tickets, which
is the surface this effort exists to discipline, and the treatment was measured harmless: prose
is not reflowed, only tables aligned and emphasis markers normalised.

The ignore file is structural rather than incidental. Prettier's PHP plugin registers itself on the
PHP extension and was measured claiming an ordinary source file in a probe — two fixers on one
concern, which the ownership rule forbids. The ignore file excludes PHP and re-includes
templates, and this was verified to reduce Prettier's claim to templates alone.
That file is what materialises the boundary.

### Absence of escape hatches

No coverage-ignore or mutation-ignore annotations, no analysis baseline, no architecture
exemptions, no lowered thresholds, no added ignore paths.

Nothing in the toolchain watches for the two ignore annotations, so the prohibition needs its
own check, expressed as a script inside the aggregate command so that it runs locally exactly
as it runs remotely.

A pinned threshold with no exemption mechanism is a stronger constraint than a perfect
threshold with a per-file waiver, because the first cannot be negotiated. Where an agent
genuinely believes a rule is wrong, the route is to raise it with a human, and that route is
documented in the quality-gate skill.

### Rollout

Nine steps on the default branch, each leaving the gate green, each extending the aggregate
command rather than editing the workflow. Continuous integration already delegates to the
aggregate command, so only three steps touch a workflow file at all: the language version, the
coverage driver, and the frontend dependency install.

The order is imposed by the green constraint, not chosen. Rewriting precedes analysis, because
the type declarations it adds are what make the maximum level reachable without hand-written
annotations. Rewriting precedes formatting, per the chain order. The Pest migration precedes
architecture, coverage and mutation, which are all capabilities of it. The architecture step
carries the code corrections it forces, because separating them would mechanically produce a
red commit.

## Testing Decisions

### What makes a good test here

This effort adds no application behaviour, so there is little external behaviour of its own to
assert. Its verification is that the chain runs green on the committed tree and that the
constraints it installs are demonstrably active — not that new behaviour exists.

Where behaviour tests *are* written, they exist to reach full coverage of code that already
exists, and they assert observable results rather than how those results are produced. A test
that asserts a formatter was invoked, or that a configuration file contains a given key, would
be testing the tools rather than this repository, and is not wanted.

### Seams

**One, existing, extended rather than replaced: the aggregate command.** It is the highest seam
available, because continuous integration delegates to it entirely — so a green local run and a
green pipeline cannot diverge. This is inherited from the foundation effort, which designated
the same seam for the same reason, and it is the one principle of that effort that survives
ADR-0001 intact.

Three verification points are added, all *inside* that seam rather than beside it:

1. **Pest architecture assertions.** A new kind of assertion — structural rather than behavioural —
   but living in the existing suite, not in a separate harness.
2. **A routing-table assertion**, checking that no web route action is a closure. This exists
   because the architecture rules cannot see route files: they operate on classes and
   namespaces, and a route file declares neither. The framework's own route registry is the
   highest available point for this property, and a single assertion there covers every route
   present and future.
3. **A forbidden-annotation check.** A script rather than a test, because no tool in the chain
   watches for the two ignore annotations and, without it, the prohibition is only an intention.

No new seam is introduced for behaviour. The tests required to reach full coverage use the
existing unit and feature suites.

### Coverage

Full line coverage of the application namespace, enforced by Pest's own minimum and
measured by pcov in the pipeline.

Widening the perimeter to routes and database directories was rejected: routes are declarations
and migrations are single-use scripts, so requiring coverage there produces tests asserting that
the framework can route and migrate — which the foundation effort already identified as wasted
work. The escape that leaves open, logic hidden in a route closure outside the measured
perimeter, is closed by *prohibiting the logic* rather than by measuring it.

Two costs are known from reading the code rather than estimated. A model's initials helper
carries a ternary whose second branch a single test would not exercise. A service provider
registers a password-defaults closure whose production branch never runs in the testing
environment, and which is only invoked at all when a password validation occurs; reaching it
means deliberately simulating production. Full coverage is cheap here, but it is not free.

### Mutation

Configured and available as its own command, deliberately **outside** the blocking gate.

The reasoning is worth preserving because the conclusion looks like a retreat and is not. Full
line coverage guarantees that every line was *traversed*, never that it was *tested* — a test
with no assertions covers perfectly. Mutation is the check that closes that gap, and it is also
the only check in the chain whose cost grows quadratically, as mutants multiplied by suite
runtime with both factors growing with the project.

Keeping it out of the aggregate command preserves the property the whole chain rests on: the
local command and the pipeline command are the same one. Letting the pipeline run a weaker set
would produce divergence in its most confusing direction, blocking a developer locally while
the pipeline stays green.

The consequence is stated rather than hidden: nothing enforces the mutation score. It is an
instrument, and an instrument nothing triggers will one day fail for reasons nobody tracked.
Pest's minimum is retained so the command self-evaluates when run, and the mutation-ignore
annotation stays forbidden — that prohibition rides on the annotation check, which is in the
gate. A scheduled job was rejected because a red that nobody reads erodes the credibility of
the gates that do matter, and this repository has neither a remote nor an audience for such a
notification.

### Prior art

The foundation effort's aggregate-command pattern is the direct precedent for the seam. Its
shipped example tests — one unit, one feature asserting the root route responds — are the
pattern for the behaviour tests written here, once translated to the new runner's style.

### Not verified

The absence of a suppression mechanism is not asserted by a test; it lives in the annotation
check and in the quality-gate skill. Nothing tests the tool configurations themselves. Nothing
asserts that a given rule is enabled — a test reading a configuration file would restate it in
a second place, and the two would drift.

## Out of Scope

- Application features. The repository after this work still has one route and one view.
- Any scheduled, nightly or periodic workflow.
- Any suppression, baseline or waiver mechanism, in any tool.
- Making the mutation score a blocking requirement.
- Branch or path coverage, and the heavier coverage driver it would require in the pipeline.
- A domain glossary. There is no domain here, only tooling; a glossary would be corrupted by
  its first entry.
- Deployment, hosting and environment configuration.
- Authentication and any user-interface component layer, both still deferred by the foundation
  effort.
- Changing the framework's own conventions. The formatter preset stays the framework's; this
  effort adds requirements, it does not substitute a different style.

## Further Notes

Every cost quoted in this document was measured against the tree rather than estimated, with
one exception stated in place: the noise phpstan-strict-rules produces on idiomatic
framework code, which cannot be known before installation.

Five further unknowns are deliberately left to implementation rather than guessed, and each is
a thing to *observe* rather than assume. Whether the architecture rules reach the test
namespace, which would put the necessarily-abstract base test case in conflict with the
prohibition on abstract classes. The mutation threshold, which is set from a measurement.
The reconciliation between Prettier's indentation setting and the editor
configuration, which must be checked file by file. The robustness of the template plugin beyond
the two files it was tried on — neither candidate is a first-party project, and this class of
tool has a deserved reputation for breaking on unusual directives. And the size of the coverage
step, the only one that is writing work rather than configuration.

Two decisions reversed themselves while this was being worked out, and both corrections are
recorded in place rather than quietly applied. The route-closure rule was first specified as an
architecture assertion and is infeasible as such. Mutation testing was first accepted into the
blocking gate and then removed from it.

The rule most likely to be regretted is named in advance, with its fallback written down, so
that reaching for it later is a decision rather than an improvisation.

The single most consequential choice here is the test runner, and the foundation effort had
already explained why: the installer substitutes it for free once and never again. It is being
changed now, at three files, precisely because that warning was correct.
