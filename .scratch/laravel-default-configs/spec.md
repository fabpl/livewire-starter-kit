# Spec: Laravel default configs

**Status:** resolved

## Problem Statement

The application service provider overrides three of the framework's defaults — immutable
dates, destructive commands prohibited in production, a password policy in production — and
stops there. Nothing records why those three and not others, which means the next one is a
matter of taste. An agent asked to "harden the defaults" has no criterion to apply and no
way to know it has finished.

Behind that gap sit defaults the framework ships that are wrong for a repository whose
stated argument is code quality and security. A model silently discards an attribute that
was meant to be assigned and silently returns `null` for one that was never loaded. URLs are
generated on whatever scheme the request reports, which behind a TLS-terminating proxy is
the wrong one. The `Host` header is trusted, so a URL built during a request is built from a
value the client controls. The client IP is the proxy's. The test suite is free to reach the
network and free to sleep for real.

There is also a well-known package that claims to answer exactly this — `nunomaduro/essentials`
— and its existence is itself part of the problem. It bundles nine defaults behind one
dependency, three of which are already here, and the remaining six are of unequal value: some
are security, one is a performance change to the semantics of every query in the application,
one is a frontend delivery choice, and one is disabled by its own author. Taking the bundle
is a decision about all nine at once. Refusing it without a stated reason leaves the
next contributor to relitigate.

The moment matters for the same reason recorded in [ADR-0001](../../docs/adr/0001-enforced-quality-gate-over-upstream-fidelity.md):
the repository holds one model with no relations, one route and one view. A default installed
today costs a line. The same default installed after the first feature costs an audit of
everything written in between.

## Solution

The defaults are repatriated call by call, never as a dependency, and only where they serve
quality or security. Performance and delivery conveniences are refused by name, so that
refusing them is a recorded decision rather than an omission.

Each default is placed at the level where its guard is provable by the suite. A guard on the
production environment is provable — a test simulates the environment and exercises both
branches. A guard on "am I running under test" is not, because no test can run outside the
suite to observe the other branch; a default needing that guard descends into the test
bootstrap, where the guard disappears entirely. Where no level makes the guard provable, the
default is installed anyway and the exception is written at the point of call.

That placement rule is the durable output of this effort. It is recorded as ADR-0005, and the
vocabulary it needs — *default*, *guard*, *provable guard*, *default taken on trust* — is
recorded in the project glossary at `CONTEXT.md`.

## User Stories

1. As a developer, I want a mistyped attribute in a mass assignment to raise an error, so that a field I meant to set is never silently dropped.
2. As a developer, I want reading an attribute that was never loaded to raise an error, so that a partial select cannot make a value look absent when it is merely unfetched.
3. As a developer, I want an authorisation check on an unloaded column to fail loudly rather than read as permissive, so that a missing value cannot be mistaken for a negative one.
4. As a developer, I want lazy-loaded relationships rejected while I work, so that an N+1 surfaces at authoring time rather than in production latency.
5. As a developer, I want strict model behaviour switched off in production, so that a relationship I forgot degrades into slowness rather than into a failed page.
6. As a developer, I want every generated URL to use HTTPS in production, so that a form action or an asset reference cannot downgrade a page served over TLS.
7. As a developer, I want the framework to reject requests carrying a host it does not recognise, so that a link built during a request cannot be built from a value the client chose.
8. As a developer, I want the real client IP recovered when the request came through a proxy on a private network, so that anything keyed by IP addresses a person rather than a load balancer.
9. As a developer, I want a forwarded IP ignored when the connecting peer is not a trusted proxy, so that an attacker cannot rewrite their own address.
10. As a developer, I want an unfaked outbound HTTP call to fail my test, so that my suite never depends on a third party being reachable.
11. As a developer, I want an unfaked outbound HTTP call to fail my test, so that no data leaves the machine while I am only running tests.
12. As a developer, I want sleeps faked in the suite, so that retry and backoff logic can be tested without the suite paying the wait.
13. As a developer, I want the suite's wall-clock cost to stay independent of how long the code waits, so that mutation runs, which replay the suite once per mutant, do not multiply a real delay.
14. As a developer, I want the choice of environment for each default to be asserted by a test, so that changing the environment gate is a visible change rather than a silent one.
15. As a developer, I want the defaults installed by the test bootstrap to be asserted by a test, so that deleting them fails the suite instead of quietly removing a protection.
16. As a developer, I want to know which framework defaults were deliberately left alone, so that I do not spend an afternoon rediscovering a decision.
17. As a maintainer, I want a criterion that decides whether a given framework default belongs here, so that the question is settled once rather than per proposal.
18. As a maintainer, I want a criterion that decides where a default is placed, so that four locations do not become four arbitrary habits.
19. As a maintainer, I want defaults that serve neither quality nor security refused by name, so that the scope of this foundation stays legible.
20. As a maintainer, I want no new dependency taken for behaviour expressible in one framework call each, so that the supply chain of a security-argued foundation stays minimal.
21. As a maintainer, I want the refusal of the well-known package explained, so that it reads as a decision rather than as ignorance of it.
22. As a maintainer, I want each default taken on trust identified as such, so that the list of things this repository has not proved is short and known.
23. As a maintainer, I want the exception written at the point of call where a default cannot be proved, so that a reader meets the caveat where the code is rather than in a document they may not open.
24. As a maintainer, I want the quality gate to stay green at every step, so that the default branch is never broken mid-effort.
25. As a maintainer, I want no coverage threshold lowered and no suppression introduced by this work, so that the gate is unchanged by an effort that only adds configuration.
26. As an agent working in this repository, I want a stated rule for where a new default goes, so that I do not put it in the most obvious place and be wrong half the time.
27. As an agent working in this repository, I want the vocabulary of guards and defaults written down, so that I use the project's terms rather than inventing synonyms.
28. As an agent working in this repository, I want to know which of the framework's protections are active, so that I do not write code defending against something already forbidden.
29. As an operator deploying this foundation, I want the trusted proxy ranges visible and commented, so that I know what to extend when the edge sits on public addresses.
30. As an operator deploying this foundation, I want the session cookie's secure flag present as a commented setting, so that enabling it is one uncommented line rather than a discovery.
31. As a future contributor, I want the placement rule recorded as a decision, so that the dispersal across four locations reads as coherence rather than as drift.
32. As a future contributor, I want the reasoning against the auto-eager-loading default preserved, so that reopening it starts from the mechanism rather than from opinion.
33. As a future contributor, I want the deferred candidates listed with the observation that motivates them, so that they are picked up deliberately rather than rediscovered.

## Implementation Decisions

### The package is not installed

`nunomaduro/essentials` is not taken as a dependency. Its behaviours are repatriated
individually where they are wanted.

Read against version `main`, the package applies nine configurables. Three are already here
and are identical in substance to what the provider already does — immutable dates,
destructive commands prohibited, a production password policy. The repository's password
rule differs from the package's by a single `max(255)` clause, which is not adopted: it
constrains nothing that the storage does not already constrain, and adding it would make the
rule differ from the one the suite currently asserts for no gain.

Four reasons, in order of weight:

The package ships three Artisan commands, two of which publish formatter and refactorer
configurations. This repository already has both, tuned harder than the package's, per
ADR-0001's position that each tool holds the strictest setting it can express. The commands
cannot be removed selectively — suppressing discovery of the package's provider suppresses
all nine configurables with it — so the dependency would be paid in order to disable half
of what it brings.

The seam already exists. The provider's private configuration method is exactly where the
package would write its calls. It offers no abstraction over that, only a published
configuration file to drive thirteen conditionals.

Each configurable is a single framework call. The ratio of dependency surface to code saved
is poor for a repository whose argument is security.

The defaults diverge. This effort adopts four of the six candidates and refuses two, and an
explicit call reads better than a divergence expressed as a `false` in a published
configuration file.

The honest cost is recorded rather than buried: package code would not be in the coverage
perimeter, which is the application namespace alone, so it would escape both the full-coverage
minimum and the mutation instrument. Repatriating means every line added is subject to both.
That is work, and it is the work this repository exists to do.

### What is repatriated, and where

| Default | Level | Guard |
| --- | --- | --- |
| Strict models | application service provider | provable — outside production |
| Forced HTTPS scheme | application service provider | provable — production only |
| Trusted hosts | middleware configuration | none available; taken on trust |
| Trusted proxies | middleware configuration | provable at the request seam |
| Stray requests prohibited | test bootstrap | none needed; the guard disappears |
| Sleep faked | test bootstrap | none needed; the guard disappears |
| Session cookie secure flag | environment example | inert; documented, not enforced |

### Strict models

`Model::shouldBeStrict()` is applied outside production and switched off in production. This
is the mirror of the two existing environment-gated rules, and it is the position the
framework's own documentation recommends.

The call is one and its effect is three: lazy loading prevented, silently discarded
attributes prevented, missing attribute access prevented. Only the third has a security
dimension — an unloaded column reads as `null`, and `null` reads as the absence of a
restriction — and that dimension is served by catching it before production rather than in
it, which is the whole argument of ADR-0001.

Applying it in production unconditionally, which is the package's position, was rejected: it
trades a risk of slowness for a risk of unavailability, which relocates the risk toward the
worse of the two rather than reducing it.

Routing violations to a reporter in production, using the three violation-handler hooks the
framework provides, was rejected as premature rather than wrong. It is the most defensible
position technically and costs three callbacks, therefore six branches under the coverage
minimum and the mutation instrument, plus an implicit dependency on a logging channel this
repository has not configured. It becomes the right answer once there is traffic, and it will
be a decision of its own then.

### Automatic eager loading is refused

`Model::automaticallyEagerLoadRelationships()` is not repatriated, and the refusal is
recorded with its mechanism because it is the package default most likely to be proposed
again.

It is a performance change, not a quality or security one, and it obtains that performance by
changing the query semantics of the whole application. That alone puts it outside the scope of
this effort.

It also cancels the decision above. Relationship autoloading is attempted *before* the
lazy-loading guard is consulted when a relation is read, and the autoload callback is
registered on every collection the framework hydrates — which is every model retrieved from
the database. With it enabled, the lazy-loading violation never fires, in exactly the case it
exists to catch.

Two variants were considered and rejected. Enabling it in production only would be a clean
complement — the suite catches the N+1 outside production, autoloading absorbs it inside —
but it would activate a deep change to query behaviour exclusively in the one environment no
test exercises, and its degraded mode is not neutral: touching a relation on one element
loads it for the entire collection, which is a memory profile nothing would have observed
beforehand. Enabling it everywhere while dropping the lazy-loading guard as inert is what the
package effectively delivers; it trades a guard that forbids for a mechanism that compensates,
and on a foundation for agent-written code the goal is that the author learns to declare the
eager load, not that a mechanism silently forgives its absence.

The accepted cost: N+1 queries that escape the suite remain N+1 queries in production. Latency
is paid in exchange for signal.

### Forced HTTPS scheme

`URL::forceHttps()` is applied in production only.

It performs no redirection; it acts on generated URLs. It is adopted because it is the one
remaining candidate that is unambiguously security: a URL generated over `http` on a page
served over `https` is mixed content — a form posted in the clear, and a session cookie sent
outside TLS where the secure flag is not armed.

It is robust where trusted proxies are fragile. Proxy trust depends on an address list that
has to be maintained; forcing the scheme depends on nothing. The two are kept together rather
than treated as alternatives.

The accepted cost: an application deployed with the production environment but served over
plain HTTP generates broken URLs, and the symptom is confusing. It is real and rare, and the
remedy is one line to comment out, documented by the test that pins it.

### Trusted hosts

The trusted-hosts middleware is enabled with no argument.

The vulnerability it closes is concrete and is lying in wait in any foundation destined to
receive authentication. Within a request, URLs are generated from the request's host rather
than from the configured application URL, so a password-reset flow driven with a forged host
header produces a valid, token-bearing link pointing at an address the attacker chose. Forcing
the scheme protects nothing here — it constrains the scheme, not the host.

No argument is passed because the framework derives the value from the configured application
URL and its subdomains, which is precisely what a foundation can decide on behalf of its
descendants. The framework also disables the middleware in the local environment and under
test, which removes the two expected objections — broken local development on a loopback
address, and a broken suite — without this repository doing anything.

This is the one default in the effort that no level makes provable, for the same reason it is
painless. The exception is written at the point of call rather than left implicit, per the
placement rule. It is accepted because the ratio is overwhelming: one line against an account
takeover.

### Trusted proxies

Proxy trust is configured with the private address ranges and the loopback address, and with
a comment naming the case the list does not cover.

The asymmetry that decides this is not intuitive and is recorded. Forwarded headers are
honoured only when the *immediate peer* is in the trusted list. Consequently, configuring
nothing fails on the safe side — the client IP is the proxy's, so everything keyed by IP
shares one bucket, which is an availability nuisance rather than a bypass. Trusting every
proxy fails on the unsafe side: if the application is ever reachable directly — an exposed
port, a health check, a firewall rule that did not land — a forged forwarded header makes an
attacker a new address on every request, which disarms IP-keyed rate limiting, poisons logs,
and defeats IP allowlists.

The private ranges are decidable without knowing the deployment, which is the point: a proxy
terminating TLS in front of the application sits on a private network in nearly every
topology — a local reverse proxy, a container network, a service mesh, an internal load
balancer. A client from the public internet has a public peer address, is therefore never
trusted, and its forwarded header is ignored.

The case not covered, and named in the comment: an edge whose addresses are public, such as a
CDN. A descendant deploying that way extends the list. They would have had to either way; the
difference is that they read it in a comment instead of discovering it in their logs.

The default trusted headers include the forwarded host, which would reopen host injection.
The trusted-hosts decision above closes it. The two are delivered together for that reason.

Making the list configurable through the environment is not possible and the attempt must not
be made. The middleware configuration callback runs when the HTTP kernel is resolved, which is
before the environment file is loaded, so an environment lookup there sees real process
variables and never a value that lives only in the environment file. Separately, environment
lookups outside configuration files return nothing once the configuration is cached, because
the loader returns early in that case. A setting that works on one deployment style and
silently does not on another is the worse of the two failures.

### Test-bootstrap defaults

`Http::preventStrayRequests()` and `Sleep::fake()` are installed by the test bootstrap, in a
per-test hook, scoped to the unit and feature suites. They are not installed by the
application service provider, which is where the package puts them.

The package has no choice: a dependency cannot reach your test bootstrap. This repository owns
its test bootstrap, and the difference is not cosmetic. The package guards both on "am I
running under test", which is always true while the suite runs. A mutant that removes that
condition, making the call unconditional, cannot be killed by any test, because no test can
run outside the suite to observe the difference. That would put a condition inside the
measured perimeter that this repository's own gate is structurally unable to prove.

Installing them in the test bootstrap removes the condition rather than covering it: the code
is reachable only under test, so no guard is needed. The test tree is outside the coverage
perimeter, so the move also costs nothing in coverage.

Prohibiting stray requests is adopted on its merits — an unfaked outbound call in a test is
slow, intermittent, and a disclosure to a third party. Faking sleep is adopted for the gate
rather than for the suite: the mutation command replays the suite once per mutant, so one
second of real waiting becomes minutes, and the symptom presents as "the gate is slow" rather
than as "someone wrote a sleep". Its known weakness is recorded: unlike the stray-request
prohibition, which raises, it substitutes silently, so a test purporting to verify a delay
would pass vacuously. The framework's assertion on recorded sleeps is the remedy and it has
to be reached for deliberately.

The browser suite is excluded from both. It is already outside the coverage measurement, it
has its own notion of waiting, and nothing gains from imposing this one on it.

### Session cookie

The environment example gains a commented setting for the session cookie's secure flag, in the
session block, following the file's existing convention for commented settings.

The framework resolves that setting from the environment with no fallback, and the example
file does not define it, so the session cookie ships without the secure flag.

Enforcing it from the provider in production — a fourth environment-gated rule, mirroring the
forced scheme — was specified and rejected by the maintainer in favour of the commented line.
The consequence is recorded as a known and accepted gap rather than as an oversight: a
commented line documents without constraining, so the foundation ships a session cookie
without the secure flag, and it is an operator who uncomments it. Writing an active value
instead would break local development over plain HTTP, since a browser refuses to store a
secure cookie on an unencrypted origin.

Two adjacent settings are deliberately left alone, so that they are not read as omissions.
Session payload encryption stays off: the store is server-side by default, so encryption
re-encrypts the whole payload on every request to protect data that never leaves the database.
The same-site policy stays lax; the stricter value looks like hardening and is a known trap,
since every return from an external identity provider and every inbound link to an
authenticated page arrives unauthenticated.

### Aggressive prefetching and unguarded models are refused

Aggressive asset prefetching is refused on the same criterion as automatic eager loading — it
is delivery performance, neither quality nor security — and on a second one specific to this
repository: the build output is not committed and the aggregate command does not build, so no
feature test can render the asset directive during a gate run. It is the one candidate for
which no acceptable location exists, since the only place its effect is observable is the
suite that the measurement excludes. It is also a decision about an asset graph that does not
exist yet; a foundation is entitled not to take it on behalf of its descendants.

Unguarded models are refused as flatly contradicting the strict-model decision above. The
package disables this one by default as well.

### The placement rule

ADR-0005 records the rule that decided the placements above, in the terms the glossary
defines: a default is installed at the level where its guard is provable; a default whose
guard would be unprovable descends to the level where the guard disappears; and where no level
makes it provable, it is installed anyway with the exception written at the point of call.

It qualifies on all three of the criteria this project applies to decision records. It is
surprising without context — the dispersal across four locations reads as incoherence
otherwise, and the obvious reference says the opposite. It is a genuine trade-off, weighed
between a provider holding an unprovable guard and a test bootstrap holding none. And what is
costly to reverse is not the two calls but the rule, once agents have followed it across
twenty defaults.

The alternative considered was a line in the agent instruction index pointing at a
document. It is cheaper and states only the *what*; the *why* — the provability of the guard —
is what allows a new case to be decided, and it is exactly what the short form drops.

### Project glossary

This effort adds to the project glossary, because it produced a load-bearing concept that has
no written name and that decided four of its questions. The glossary did not exist when this
document was written; the interface effort created it at `CONTEXT.md` in the interval, so what
was a creation becomes four entries appended to four that are already there.

Four entries: **default**, a framework behaviour the foundation replaces at boot for
everything built on it; **guard**, the environment condition under which a default applies;
**provable guard**, a guard whose branches the suite can both exercise, which is what
determines the level at which a default is installed; and **default taken on trust**, a
default no level makes provable, installed regardless, with the exception written at the point
of call.

The glossary stays free of implementation detail, per the project's domain documentation
convention. The quality gate spec's position that "there is no domain here, only tooling" is
superseded rather than contradicted: the terms above are specific to this foundation's way of
working and are not general programming vocabulary.

## Testing Decisions

### What makes a good test here

Assertions target observable framework behaviour, not the configuration that produced it. A
test that reads a configuration value and asserts it equals what the provider wrote restates
the source in a second place, and the two drift. The exception is the session cookie setting,
which is not asserted at all — see below.

Each environment-gated default is pinned by two cases, one per branch. Two cases are also what
the mutation instrument needs: they kill both the mutant that drops the negation and the mutant
that removes the condition.

### Seams

**No new seam is introduced.** The root seam is unchanged: the aggregate command, inherited
from the quality gate effort, identical locally and in continuous integration.

Three verification points inside it, all pre-existing:

1. **Rebooting the provider under a simulated environment.** Established by the existing
   password-defaults test, which switches the environment and constructs the provider again.
   It carries the strict-model and forced-scheme defaults.
2. **A feature-suite HTTP request.** Established by the shipped example test. It carries the
   trusted-proxies default, which is asserted by sending a request whose peer address is
   private and whose forwarded address is public, and a second whose peer address is public.
3. **An ordinary feature test observing state the bootstrap installed.** It carries the two
   test-bootstrap defaults.

The third deserves its reasoning. Those two settings live in the test bootstrap, where nothing
watches them: deleting the hook fails no test, and the protection disappears in silence, which
is precisely the class of drift this repository is built against. Two ordinary assertions
close it without a new seam.

### Depth of proof

The strict-model default is pinned by its guard and by one of its three consequences — the
mass-assignment exception raised when an unfillable attribute is passed, which needs neither a
database nor a relationship.

The other two consequences are taken on the framework's word. Proving the framework's own
fan-out would retest upstream what upstream already tests; what this repository decides is
which environments, and that is what the test pins. Proving the missing-attribute consequence
would require a database round trip and the refresh trait, which the suite does not currently
use. Proving the lazy-loading consequence would require adding a relationship to the single
model in the foundation purely so that a test can violate it — an addition that looks rigorous
and leaves an orphan relationship in a foundation's central model permanently. It is refused.

The consequence to state plainly: the best-known effect of strict models, the prohibition on
lazy loading, is asserted by no test here.

The forced-scheme test sets the application URL explicitly rather than depending on the
ambient one, which the test configuration does not pin and which therefore comes from an
environment file that may be absent in continuous integration.

### Not verified

Three things, by observation rather than by omission.

**Trusted hosts.** The framework disables the middleware under test, so the suite cannot
observe it. This is the one default taken on trust, and the ADR names it as the category's
only member.

**The session cookie setting.** A commented line in an example file has no behaviour to
assert. Even enforced, the observable — a response header carrying the secure attribute —
would be out of reach, because the suite's session driver is the array driver and no session
cookie is emitted at all.

**The lazy-loading consequence**, per the section above.

### A mechanism the effort depends on

Nothing resets the static strict-model flags or the password defaults between tests; the
framework's teardown flushes the faked sleep and both middleware states, and not those. What
makes the suite safe anyway is that every test boots a fresh application, so the provider runs
again and reimposes its values before anything else executes. That is already what makes the
existing password-defaults test safe, and it is invisible.

It is recorded here because it is load-bearing and conditional: the net exists only for
defaults installed in a provider. A default installed elsewhere does not get it.

### Prior art

The password-defaults test is the direct pattern for both provider tests — same structure,
same environment switch, same two-case shape. The shipped example test is the pattern for the
request-seam test. Nothing here needs a form the suite does not already contain.

## Out of Scope

- Installing `nunomaduro/essentials`, or any dependency, for these behaviours.
- Automatic eager loading of relationships, in any environment.
- Aggressive asset prefetching, and any Vite prefetch strategy.
- Globally unguarded models.
- Routing strict-model violations to a reporter in production, and the logging channel that
  would require.
- Enforcing the session cookie's secure flag from application code.
- Session payload encryption and any change to the same-site policy.
- Making the trusted proxy list configurable at deploy time.
- `failOnDeprecation`, `failOnWarning` and `failOnNotice` in the test runner configuration.
  Deferred deliberately, with its observation recorded under Further Notes.
- Runtime deprecation logging, which the environment example currently routes to nowhere.
- Rate limiting, authentication routes, and anything that would give the recovered client IP
  something to key.
- Application features. The repository after this work still has one model, one route and one
  view.
- Any lowering of the coverage minimum, any suppression annotation, any analysis baseline.

## Further Notes

Every framework behaviour asserted in this document was read from the installed source at
version 13.23.0 rather than from documentation, including the three that decide questions:
that relationship autoloading is attempted before the lazy-loading guard and that the autoload
callback is registered on every hydrated collection; that the trusted-hosts middleware
disables itself outside production while the trusted-proxies middleware does not; and that the
middleware configuration callback runs before the environment file is loaded.

One classification in the originating session was wrong and is corrected here rather than
carried forward. Trusted proxies was recorded as a default taken on trust, by analogy with
trusted hosts. It is not: the middleware has no environment gate, runs under test, and is
provable at the existing request seam. The decision is unchanged — the private ranges stand —
but the effort gains a test and the taken-on-trust category shrinks to one member. The analogy
was the error, not the reading.

One candidate is deferred with the observation that motivates it, so that it is picked up
deliberately. Runtime deprecations are currently routed to nowhere by the environment example,
and static analysis covers only deprecated API used directly in this tree — not deprecations
the framework raises from inside in response to how it is called. Turning those into test
failures belongs to the gate rather than to the framework's defaults, and motivating it
properly means discussing how to harden the gate without making it brittle to a third-party
package's deprecations. That is its own spec.

The `max(255)` clause the package adds to its password rule is not adopted, and this is the
only point where this effort knowingly ends up with a rule weaker than the reference. It
constrains nothing the storage does not already constrain.

Three of the nine configurables examined were already present before this effort and are
untouched by it. That they match the package's almost exactly is worth recording as
corroboration rather than as coincidence: the position taken independently here and the
position taken by the reference agree on the three that were easy, and this effort's
contribution is the criterion that decides the six that were not.
