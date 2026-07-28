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
20. As a developer, I want the application driven in a real browser, so that a defect appearing only once the page executes is caught here rather than by a user.
21. As a developer, I want console errors treated as test failures, so that a page that renders is never mistaken for a page that works.
22. As a developer, I want my templates, styles, scripts and workflow files formatted mechanically, so that the frontend is held to the same standard as the backend.
23. As a developer, I want utility classes sorted automatically in my templates, so that class order is never a matter of opinion.
24. As a developer, I want the formatter for templates and the formatter for PHP to have an enforced boundary, so that they cannot fight over the same file.
25. As a maintainer, I want each tool to own its concern alone, so that the chain converges instead of oscillating.
26. As a maintainer, I want fixers and verifiers separated, so that a broken rule is documented by its failure rather than repaired in silence.
27. As a maintainer, I want machine-owned manifests and lockfiles left to their owners, so that installing a dependency does not turn the pipeline red.
28. As a maintainer, I want tool thresholds pinned rather than aliased, so that upgrading a tool cannot raise the bar without a commit.
29. As a maintainer, I want browser scenarios kept out of the coverage measurement, so that a line a browser walked through cannot pass for a line a test asserts.
30. As a maintainer, I want no suppression mechanism available anywhere, so that a failing check is fixed rather than filed away.
31. As a maintainer, I want every tool version pinned in a lockfile, so that the chain is reproducible on a fresh machine.
32. As a maintainer, I want each step of the rollout to leave the gate green, so that the default branch is never broken mid-effort.
33. As an agent working in this repository, I want the standard I am held to expressed as executable configuration, so that I can verify my own work before reporting it done.
34. As an agent working in this repository, I want to be told which repairs are forbidden when a check fails, so that I fix the code instead of weakening the gate.
35. As an agent working in this repository, I want a way to raise a disagreement with a rule, so that my only options are not "comply" and "circumvent".
36. As a future contributor, I want the departure from the upstream kit recorded as a decision, so that I do not mistake it for neglect.
37. As a future contributor, I want the reversal on the test runner recorded as a decision, so that I do not treat it as an accident.
38. As a future contributor, I want the measurements behind each choice preserved, so that reopening a decision starts from evidence rather than from taste.

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
| Class finality | — (Rector, for test cases only) | Pest architecture tests |
| Immutable date construction | Rector | — |
| Dead code and type declarations | Rector | Larastan |
| Templates, styles, scripts, workflows | Prettier | — |
| Dependency manifests and lockfiles | Composer, npm | — |

Class finality is the one concern in this table with no general fixer, and the reason is that
no tool in the chain offers a correct one. Pint's rule is purely syntactic and would finalise
the abstract base test case, which exists to be extended, so it stays excluded. Rector, which
does perform inheritance analysis, turns out to ship finalisation only for test-case classes —
that narrow rule is enabled, and it is the whole of the row. Everything else is left to the
architecture verifier, which fails on a non-final class rather than repairing it. A concern
with a verifier and no fixer is a weaker position than the rest of the table but not a broken
one: nothing can drift silently, the failure simply has to be repaired by hand.

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
annotation removal, nullable-type normalisation, useless control-flow removal, and string and
heredoc consistency.

Four exclusions are load-bearing and must not be quietly reinstated. Class finality and
return-type insertion belong to Rector. Immutable date construction belongs to Rector's Carbon
set: the two do not merely overlap, they disagree about the destination — Rector rewrites
towards the framework's own date class, Pint's rule towards the native immutable one. Two
fixers on one concern, and the chain converged only because Rector always reached the code
first. Native function invocation is
excluded because it wants a leading separator on global functions exactly where name importing
wants an import statement — the two are in direct conflict, and the micro-optimisation does not
justify the noise. Name importing is therefore limited to classes.

### Comments — Pint

Pint's annotation-only rule does not tidy docblocks; it forbids comments. Any block
or line comment carrying no annotation at all is deleted. Measured on the current tree, it
removes prose docblocks whose entire content restates the method name, and a commented-out
import, while preserving every typed annotation.

What survives the rule is decided by the character `@` and nothing more. The three escape
prefixes — `@note`, `@warning`, `@todo` — are a documentation convention rather than a
mechanism: the implementation returns early on any comment containing an `@`, so an email
address in an example preserves a comment exactly as well as a prefix does. The hatch is
therefore wider than a list of three, and the prefixes are worth writing because a reader
recognises them, not because the tool distinguishes them.

The rule is still the one best aimed at the stated problem, because filler commentary is what
agents produce in volume and nothing else in the chain sees it, and because the wide hatch is
not a silent one: preserving an explanation takes a deliberate edit that shows up in a diff,
which is what the prefixes were asked to cost. Configuration files are exempt by the rule's own
design, since configuration relies on commentary to document itself.

The shape of a preserved comment is constrained by the same mechanism, and the difference is
not cosmetic. A `//` block is one token per line, so a prefix preserves only the line carrying
it. A `/* … */` block is a single token, so one prefix anywhere preserves all of it. A `/** … */`
docblock is rebuilt from its annotation lines, so prose inside one is lost even with a prefix.
Explanation running to more than a line has to be a `/* @note … */` block.

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

Turning a group off is not enough on its own, because the recommended set carries individual
rules in past the groups they belong to. Four are therefore named rule by rule: increment
style and strict-type declarations, which belong to Pint; strict equality, which belongs to
Pint too; and the rule that stamps a `@see` docblock on every class pointing at its test,
which is the docblock-writer conflict arriving by a different door. What the recommended set
is kept *for* is the one rule nothing else in the chain provides — finalisation of test-case
classes, the whole of what Rector contributes to the finality row of the ownership table.

Automatic version detection here sits alongside a pinned analysis level, and that is
consistent rather than contradictory: an alias moves when the *tool* updates, unrelated to the
code, whereas version detection moves when *the framework* is upgraded — which is precisely
when rewriting is wanted.

The perimeter is the tree including Rector's own configuration file, which was reaching only
Pint and so was the one file the tool that rewrites everything did not rewrite. One file
remains outside it and cannot be brought in: `artisan` carries no extension, and Rector filters
by extension before it filters by path, so naming the file achieves nothing. It is reached by
Pint through the lint script and by Larastan through an explicit path, which leaves structural
rewriting as the only part of the chain it escapes. The gap is stated rather than closed.

### Static analysis — Larastan

The level is pinned as an integer at the current maximum rather than written as the alias, so
that a release adding a level cannot raise the bar on a dependency update nobody connected to
it. Raising the bar should be a commit. Measured: the maximum level already passes with zero
errors, so the raise itself is free today.

Two official extensions — phpstan-strict-rules and phpstan-deprecation-rules — are added
because they live *beyond* the maximum level and no level
contains them — they are the part that bites. The analysed perimeter widens to tests, the
public directory and the whole bootstrap directory, and to the two PHP files at the root that
no analyser was reading: `artisan`, named as a file because it carries no extension, and
Rector's own configuration. The cost estimated in advance is one
error, in an example test this effort deletes anyway. Both halves of that estimate turned out
wrong, and the correction is recorded below rather than written over it, so that the estimate
and what it actually cost stay legible against each other.

No baseline. The project starts at zero errors so there would be nothing to record, and
installing the mechanism would only give agents somewhere to file future failures.

One cost in this spec was *not* measured in advance, and it is flagged rather than buried: the
noise phpstan-strict-rules produces on idiomatic Laravel code could not be evaluated before
installation. If the result is unreasonable, phpstan-strict-rules is removed and the removal
recorded — never kept with its findings suppressed.

Measured at installation, that noise is negligible and the extension is kept. The whole raise —
level 7 to the maximum, both extensions, the widened perimeter — reports five errors. Three are
the same finding in kit-shipped configuration: an `array_filter` called without a callback, once
in `config/app.php` and twice in `config/database.php`. All three take Laravel's own `filled(...)`
as the second argument, which states an intent the loose semantics could only imply — and which
is deliberately not the same predicate. The callback-less form drops everything falsy, so it
discarded the string `"0"`; `filled(...)` keeps it and discards whitespace-only strings instead.
That is a real change of behaviour in an effort that adds none elsewhere, and it is the right
one here: an application key and a certificate authority path are values where `"0"` is as
meaningful as any other character and a string of spaces is not a path. It is recorded because
a rule that forces a rewrite does not get to decide the semantics silently. The other two
are the shipped feature test calling `$this->get()`, which the functional style replaces with the
plugin's `get()` function — the closure Pest binds is not a method, so the analyser was right
that the call had no receiver it could see. Nothing was suppressed and no signature widened.

That last pair is where the advance estimate above was wrong twice over. Widening the perimeter
cost two errors rather than one, both on the same line; and the example test was not deleted by
the Pest migration, which rewrote it in place and carried the receiver across, so this effort
had it to correct rather than gone. Neither miss changes a decision — the corrected cost is
still trivial — but the estimate was of the wrong shape, not merely the wrong size.

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

Three collisions were named in advance and five were found, and the difference is recorded
rather than written over. The two unnamed ones are the shipped factory and the shipped seeder,
non-final like everything else the kit ships — missed because the reading that produced the
list looked at the application namespace and the rules apply to every namespace the dependency
manifest declares, which here is three. Neither cost anything but a keyword. What one of them
cost afterwards is the more interesting half: finalising the factory let Rector privatise its
cached-password property, and a private property is one the analyser can reason about, so the
maximum level immediately reported a `null` in its type that nothing ever assigns. The type was
narrowed to match. That is three tools agreeing in sequence rather than a rule forcing a
change, and it is the clearest evidence so far that the chain is one thing and not five.

The finality row of the ownership table is weaker in practice than it reads, and the measurement
belongs here rather than being left to be rediscovered. Rector's contribution to that row is
finalisation of test-case classes, and the functional test style leaves no test classes — so on
this tree Rector finalises **nothing**. Verified by un-finalising two classes and running it:
zero changes. Every `final` keyword in the tree was written by hand under pressure from the
architecture rules, which report a non-final class rather than repairing it. The row therefore
has a verifier and, today, no fixer at all. Nothing can drift silently, but the repair is manual
in every case rather than in the residual ones.

The presets are invoked by constructing Pest's preset object inside an architecture test rather
than through the documented `arch()->preset()` chain, and the reason is a genuine collision
between two members of the gate. That chain resolves through a class Pest annotates with a
union `@mixin`, which the analyser does not follow, so at the maximum level it reports the
call as undefined — eight errors, on four lines that are correct. Both available repairs were
forbidden: no ignore annotation, and no path removed from the analysed perimeter. Constructing
the object directly is neither, and it was verified equivalent rather than assumed — the same
sixty assertions, and a deliberately non-final class still fails the suite. The cost is that
this reaches an API Pest marks internal; the mitigation is that the documented chain reaches
the same class by a longer route, so an upstream change breaking one would break both.

Whether the rules reach the test namespace was the effort's first open unknown, and it is
settled by observation: **they do not.** Pest builds its perimeter from the namespaces the
dependency manifest declares and then discards every one whose directory is under `tests`, so
`Tests\` is outside the rules by construction. Probed rather than read off the source — a class
placed in that namespace violating four strict expectations at once, abstract and non-final
with a protected method and a loose comparison, is not seen. The conflict the spec anticipated,
between the necessarily-abstract base test case and the prohibition on abstract classes, does
not arise. It also means the largest body of code in the project is covered by the analyser and
not by the architecture rules, which is a gap this effort states rather than closes.

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

Prettier owns templates, styles, scripts and workflow files. It
does not own dependency manifests or lockfiles: those already have owners that rewrite them on
every install. The measured disagreement there is a single missing final newline — trivial in
itself, and exactly the kind of recurring unrelated failure that gets an entire gate switched
off after three months.

Documentation was included and then excluded, and the reversal is recorded rather than quietly
applied. The original reasoning was that the issue tracker holds agent-authored tickets, which
is the surface this effort exists to discipline, and that the treatment was measured harmless:
prose is not reflowed, only tables aligned and emphasis markers normalised. The maintainer
reversed it during implementation. Those two edits are harmless to a machine but not free to a
human — realigning a table and rewriting `*maximum*` as `_maximum_` is churn on the one surface
where nobody was arguing about formatting — and prose is where the argument for a mechanical
formatter is weakest, because there is no reviewer time being lost to it. Markdown is therefore
excluded, and the reasoning sits in the ignore file next to the rule.

This is worth noting as evidence about the escalation route rather than as a footnote about
markdown. The spec claims that disagreement with a rule is resolved by a human changing the
configuration deliberately, never by routing around it. That is what happened here, on the
first rule anyone disagreed with.

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

Ten steps on the default branch, each leaving the gate green, each extending the aggregate
command rather than editing the workflow — the last two extend neither, since mutation and
browser testing are commands beside it. Continuous integration already delegates to the aggregate
command, so only three steps touch a workflow file at all: the language version, the coverage
driver, and the frontend dependency install.

The order is imposed by the green constraint, not chosen. Rewriting precedes analysis, because
the type declarations it adds are what make the maximum level reachable without hand-written
annotations. Rewriting precedes formatting, per the chain order. The Pest migration precedes
architecture, coverage, mutation and browser testing, which are all capabilities of it. The
architecture step carries the code corrections it forces, because separating them would
mechanically produce a red commit. Browser testing comes last: its exclusion from the coverage
measurement can only be demonstrated once there is a coverage measurement to be excluded from.

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

   The registry holds more than this repository declares, and that was measured rather than
   assumed: of the thirteen routes registered under test, six are closures and every one of
   them belongs to a dependency — the framework's health endpoint and its two local-storage
   routes, and three of Livewire's asset routes. The one route this repository declares is not
   among them, because `Route::view` registers a framework controller rather than a closure —
   so the assertion passes today on merit and not by accident. None of the six can be rewritten
   here, so the assertion resolves each
   closure to its declaring file and reports only those outside `vendor`. The filter is the
   tree rather than the route file, which is what makes it hold for a route file that does not
   exist yet; a closure whose origin cannot be resolved is reported rather than skipped. The
   assertion was verified to fail — a closure route added to `routes/web.php` is named in the
   failure by its URI, and the file was then restored.

   What that scoping gives up is stated rather than glossed. The assertion says "no closure
   declared outside `vendor`", which is narrower than "no route action is a closure": a route
   file here could bind a closure that a dependency constructed, and it would pass. The
   narrowing is forced — six vendor closures are registered and none of them is rewritable —
   and reaching the residual hole takes a deliberate act rather than the inattention the rule
   exists to catch.

   **The rule targets web routes only, and the shipped console closure stays a closure.** That
   is the decision, and the reasoning is that the alternative buys nothing mechanical.
   Artisan commands are not in the route registry at all, so no assertion at this seam can see
   them; rewriting the shipped `inspire` command as a class would remove today's instance
   without preventing tomorrow's, since nothing in the chain would then forbid the next console
   closure either. The gap is therefore stated rather than closed: logic placed in
   `routes/console.php` is outside the measured perimeter and outside this assertion, and
   closing it would need a second assertion against the console registry — a deliberate
   addition for a human to make, not one to take in passing on a ticket that named web routes.
3. **A forbidden-annotation check.** A script rather than a test, because no tool in the chain
   watches for the two ignore annotations and, without it, the prohibition is only an intention.

**A second seam is introduced, and it is outside the gate: the running page.** The browser suite
drives the application through a real browser rather than through the framework's HTTP kernel,
which is the only vantage point from which console output, script execution and built assets are
observable at all. It is a genuine seam and not a variation on the first, because everything the
aggregate command runs stops at the response body.

Its separateness is load-bearing twice over. It must not contribute to the coverage measurement,
and — since the maintainer keeps it out of the aggregate command — it must not be able to
interfere with a run of it either.

No new seam is introduced for behaviour *inside* the gate. The tests required to reach full
coverage use the existing unit and feature suites.

### Coverage

Full line coverage of the application namespace, enforced by Pest's own minimum and
measured by pcov in the pipeline.

Widening the perimeter to routes and database directories was rejected: routes are declarations
and migrations are single-use scripts, so requiring coverage there produces tests asserting that
the framework can route and migrate — which the foundation effort already identified as wasted
work. The escape that leaves open, logic hidden in a route closure outside the measured
perimeter, is closed by *prohibiting the logic* rather than by measuring it.

The browser suite is outside the measured perimeter in the other direction: it is not that its
own files go unmeasured, but that the pass which measures never runs it. A line a browser
scenario walked through must not count as covered, or the minimum becomes satisfiable by driving
pages instead of by asserting behaviour.

Two costs are known from reading the code rather than estimated. A model's initials helper
carries a ternary whose second branch a single test would not exercise. A service provider
registers a password-defaults closure whose production branch never runs in the testing
environment, and which is only invoked at all when a password validation occurs; reaching it
means deliberately simulating production. Full coverage is cheap here, but it is not free.

Measured at implementation, both costs were real and the first was named too narrowly. The
model was not one uncovered branch but nothing at all: eleven of the application's twenty-eight
measured elements were covered beforehand, and none of the model's were — `initials()` and
`casts()` alike — because no shipped test ever constructs a user. The closure behaved exactly
as read: its production branch was the only uncovered region of the provider, and the existing
suite never invoked the closure at all. Four tests in two files close both gaps, taking the
measurement from 39.1 per cent to a hundred with no suppression, no lowered threshold and no
widened perimeter. The estimate was of the wrong shape rather than the wrong size, for the
second time in this effort.

Reaching the production branch costs one thing worth recording. The rule that branch builds
ends in `uncompromised()`, which queries an external service, and a gate that calls the network
is a gate that goes red for reasons unrelated to the code. The test never reaches that call,
because the rule's own length check fails first and it returns before verifying. The
short-circuit is what keeps the network out of the gate, and it holds only while the password
the test offers is shorter than the twelve characters production demands. That is stated rather
than guarded, since guarding it would mean asserting how the rule works instead of what it
accepts.

The first attempt to establish this was itself unsound, and it is recorded because the failure
is instructive about what counts as a measurement here. Forbidding stray requests and observing
the test still pass proves nothing: the framework's verifier wraps its request in a `try` and
reports the exception, so a blocked request is swallowed and the validation continues as though
the password were uncompromised. A probe whose negative result is indistinguishable from
success is not evidence. Faking the client and asserting nothing was sent is, and it was
confirmed to discriminate rather than merely to pass — the same probe on a password long enough
to survive the length check records a request to the external service.

The command gains its first prerequisite beyond an install: the threshold cannot be evaluated
without a coverage driver. The pipeline installs pcov, and a local machine running Xdebug needs
its coverage mode switched on. A run that reports coverage could not be obtained is the one
failure in this chain that says nothing about the code, so the quality-gate skill names it as
such — an agent that met it without that note would have a lowered threshold within reach and
no reason not to reach for it.

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

The command is `composer mutate`, and it states no perimeter of its own. Pest reads the paths
to mutate from `phpunit.xml`'s `<source>` when the command line names none, so the mutated
perimeter is the measured perimeter by construction rather than by a second declaration free to
disagree with the first. What the command does say is `--everything`, which is not a widening:
it means "filter by no class list", and it is the flag that replaces the `covers()` annotations
this repository does not write.

**Measured on 28 July 2026: 72.00 per cent, on 25 mutants across the two application files.**
Seven escape. The minimum is pinned at that measurement — `--min=72` — and it was verified to
discriminate rather than merely to pass: at `--min=73` the command exits one, at `--min=72` it
exits zero. Parallel and serial runs agree on the score exactly, so the number is a measurement
and not a sample.

`--covered-only` turns out to be load-bearing, and the reason is not the one it would be in a
project without full coverage. At a hundred per cent line coverage it ought to exclude nothing,
and it excludes five mutants — three on line 27 and two on line 28 of the model, which are the
`#[Fillable]` and `#[Hidden]` attributes. An attribute is not an executable statement, so no
coverage driver ever records it as traversed, and the mutator nonetheless generates mutants
there. Without the flag the same suite measures 60.00 per cent on 30 mutants, and the twelve
points of difference are entirely those five.

That is stated rather than glossed, because the flag is doing something narrower than "restrict
to what the tests reach". Those five mutants are killable — removing an item from the
mass-assignment list is a behaviour change a test could detect — and the flag hides them behind
a property of the coverage format rather than a property of the tests. The instrument is
therefore blind to attribute arguments, which is the cost of taking the perimeter from the
coverage report; the alternative is a score dragged down by mutants no driver can ever mark
covered, which would make the minimum uninterpretable. Reaching those five takes a deliberate
run without the flag, not a different configuration.

The seven escaping mutants are recorded rather than repaired, because raising the score is
writing tests and this is a configuration step. Two are in the model's `initials()` — the
`true` argument to `Str::initials` and the `> 1` in the ternary — which is precisely the branch
the coverage step could only hold by convention, now held by an instrument that names it. Three
more are the model's `casts()` array, whose contents no assertion inspects. The last two remove
service-provider calls, `Date::use(…)` and `DB::prohibitDestructiveCommands(…)`, which the suite
boots and never observes the effect of. Every one is a real gap in the assertions rather than a
false positive, which is the instrument working.

### Browser scenarios

Configured and available as its own command, deliberately **outside** the blocking gate, on the
same terms as mutation and for a different reason.

The gap it closes is one no other check in the chain can see. Static analysis reads types, the
architecture rules read namespaces, the feature suite asserts a response — every one of them
stops before the page executes. A template that renders and then throws in the browser, a script
that fails to boot, an asset the build did not emit: none of that is visible to the gate, and all
of it is visible to the first person who opens the application.

Two things are asserted, in that order. First, that nothing was written to the browser console —
the cheapest assertion available and the one most easily lost, because a page whose console is
full of errors still returns a successful response and still satisfies every assertion the
feature suite makes. Second, the success paths of the navigations the application offers. Only
success paths: with one route today, the value of the scenarios is a harness ready for the tenth
route, not the coverage they provide for the first.

The suite is excluded from the coverage measurement, which is a requirement rather than a
convenience. Nothing distinguishes a line a browser walked past from a line a unit test asserts,
so counting browser traversal would let the hundred per cent minimum be satisfied by driving
pages — ticket 09's requirement reduced to a formality. The exclusion is mechanical: the browser
tests live in their own suite and the run that measures coverage does not execute it.

Measured at implementation, the exclusion is load-bearing rather than notional. The scenarios
live in a third `Browser` suite and the gate's test step names `Unit` and `Feature` explicitly;
the same runner invoked without those names collects twelve tests instead of eleven, so the
naming is what does the work and not a coincidence of layout. The gate's own numbers are
unchanged by the addition — eleven tests, sixty-eight assertions, a hundred per cent — which is
the statement that no browser scenario reached the measurement.

The mutation command needs the same names for the same reason, and this was found on integrating
the two commands rather than anticipated. Mutation reads its perimeter from the coverage
`<source>` and selects mutants with `--covered-only`, so a suite it runs is a suite that can
qualify a line as a mutation target — the substitution the paragraph above forbids, arriving
through the instrument beside the gate instead of through the gate. Measured on this tree the
names change nothing: twenty-five mutants and 72.00 per cent with them and without them, since
every application line is already covered by the unit and feature suites and the browser scenario
qualifies none that was not qualified already. They are there for the tree that has a line only a
browser reaches.

What the console assertion actually sees was probed rather than assumed, and it is narrower than
the sentence above implies. The plugin captures the console by replacing `console.log` on the
page and by listening for the window's `error` event. A `console.log` and an uncaught exception
are therefore caught; `console.error`, `console.warn`, and a failed resource load — a stylesheet
or a script the build did not emit, requested and answered with a 404 — are **not**. All five
were probed against the running page, each by adding the defect to the view and observing the
suite, and each probe was checked to discriminate rather than merely to pass.

That costs the third item in the list of defects this suite was meant to close: a script that
throws is caught, an asset that is missing is not. It is recorded rather than repaired, because
the repair would mean writing a second console capture alongside the plugin's — a fixer beside
the owner of the concern, which is the arrangement the ownership rule exists to prevent. The
plugin does own one slice of it, `assertNoBrokenImages`, and that slice is empty here: the
shipped view declares no `img` element, so the assertion would pass on a page with no images to
break and report a guarantee it never exercised. It is left out for that reason rather than
overlooked, and it is the assertion to reach for first when the application gains an image. The
honest scope of the suite today is the executing page, not the manifest behind it. If the
application grows an interface whose assets can plausibly go missing, this is the second decision
to revisit after the one below.

Keeping it out of the aggregate command is the maintainer's decision, taken against the
recommendation in this document, and both sides of it are recorded. Against: the chain's own
argument, that an instrument nothing triggers will one day fail for reasons nobody tracked — the
cost accepted for mutation only because mutation's runtime grows quadratically, which this does
not. For: a browser suite inside the gate means installing a browser binary on every pipeline
run, for an application that currently has a single page. The trade is explicitly provisional. If
the application grows a real interface, this is the first decision to revisit.

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
- Making the browser suite part of the blocking gate, and installing a browser binary in the
  pipeline.
- Browser assertions beyond console hygiene and success-path navigation: no failure paths, no
  visual regression, no accessibility auditing, no cross-browser or responsive matrix.
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

Seven further unknowns are deliberately left to implementation rather than guessed, and each is
a thing to *observe* rather than assume. Whether the architecture rules reach the test
namespace, which would put the necessarily-abstract base test case in conflict with the
prohibition on abstract classes — observed, and answered above: they do not, and the conflict
never arises. The mutation threshold, which is set from a measurement — measured, and answered
above: 72 per cent, and the measurement turned up a second thing nobody had asked about, which
is that a hundred per cent line coverage does not make `--covered-only` a no-op.
The reconciliation between Prettier's indentation setting and the editor
configuration, which must be checked file by file. The robustness of the template plugin beyond
the two files it was tried on — neither candidate is a first-party project, and this class of
tool has a deserved reputation for breaking on unusual directives. And the size of the coverage
step, the only one that is writing work rather than configuration — measured, and answered
above: four tests in two files, from a starting point of 39.1 per cent.

The last two belonged to the browser suite and could not be measured until something was
installed. Both are now answered, and one of the answers is not the one the question expected.

Whether the browser plugin's own dependency resolution is compatible with the single point at
which Pest, PHPUnit and `laravel/pao` already meet — the migration ticket found that point to be
exact rather than roomy. It is: the plugin resolved and installed against the existing lock
without moving any of the three, and the gate stayed green immediately afterwards. The
point turned out to be roomier than the migration ticket's experience suggested.

And whether a browser scenario needs built frontend assets present, which would make the
command's prerequisites larger than a binary download and turn "run this command" into "run
these four". Strictly it does not — the shipped view falls back to an inlined stylesheet when no
build manifest exists, so the suite passes on an unbuilt tree. That pass is worth less than it
looks: on an unbuilt tree the `@vite` directive never runs, the application's script is never
requested, and the scenarios exercise a fallback branch rather than the page a user would get.
The command therefore builds the assets itself before running, which costs about a second and
keeps the prerequisite at one download. The question's premise held even though its answer
was no.

Four decisions reversed themselves, and all four corrections are recorded in place rather than
quietly applied. The route-closure rule was first specified as an architecture assertion and is
infeasible as such. Mutation testing was first accepted into the blocking gate and then removed
from it. Documentation was first inside Prettier's perimeter and was taken out of it during
implementation. Browser testing was recommended *inside* the gate by this document and placed
outside it by the maintainer. The last two are the ones reversed by the maintainer rather than by
this document, and so the two live tests of the escalation route — in both cases the
configuration was changed deliberately rather than routed around, which is the property the
route exists to have.

The rule most likely to be regretted is named in advance, with its fallback written down, so
that reaching for it later is a decision rather than an improvisation.

The single most consequential choice here is the test runner, and the foundation effort had
already explained why: the installer substitutes it for free once and never again. It is being
changed now, at three files, precisely because that warning was correct.
