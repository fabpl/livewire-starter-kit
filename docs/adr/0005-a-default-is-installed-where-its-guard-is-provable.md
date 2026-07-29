# A default is installed where its guard is provable

The foundation replaces framework defaults call by call, and the calls do not land in one
place. Strict models and the forced HTTPS scheme are in the application service provider;
trusted hosts and trusted proxies are in the middleware configuration; the prohibition on
stray requests and the faked sleep are in the test bootstrap; the session cookie's secure
flag is a commented line in the environment example. Four locations for seven defaults reads
as four habits unless the thing that chose them is written down.

One rule chose them. **A default is installed at the level where its guard is provable by the
suite.** Two corollaries follow. A default whose guard would be unprovable descends to the
level where the guard disappears entirely. And where no level makes it provable, the default
is installed anyway and the exception is written at the point of call.

The terms are the project's: a *default*, a *guard*, a *provable guard* and a *default taken
on trust* are defined in [CONTEXT.md](../../CONTEXT.md). The rule is stated in them because it
cannot be stated without them.

The case that forces it is the prohibition on stray requests. The obvious placement is the
application service provider, beside the three defaults that were already there, guarded on
"am I running under test" — and that is where the well-known reference package puts it,
because a dependency cannot reach your test bootstrap. This repository owns its test
bootstrap, and the difference is not cosmetic. That condition is always true while the suite
runs. A mutant that removes it, making the call unconditional, cannot be killed by any test,
because no test can run outside the suite to observe the other branch. The obvious placement
puts a branch inside the measured perimeter that this repository's own gate is structurally
unable to prove. Installed in the test bootstrap, the condition is not covered — it is
removed: the file is reachable only under test, so there is no guard to write, and the test
tree is outside the coverage perimeter, so the move costs nothing there either.

The rule is not a restatement of "test your code". It decides a question testing alone does
not answer: given a behaviour that is wanted everywhere, at which level does it go so that
what remains of it is provable? The answer sometimes moves the call out of the application
entirely, which is the opposite of what the obvious reference does.

Trusted hosts is the one default in this foundation that no level makes provable, and it is
the only member of the taken-on-trust category. The framework disables that middleware in the
local environment and under test, which is precisely what keeps local development and the
suite working — and precisely what puts the behaviour out of the suite's reach. There is no
level to descend to: the middleware configuration is the only place the call exists. It is
installed regardless, because the ratio is overwhelming — one line against an account
takeover, since a password-reset flow driven with a forged host header produces a valid,
token-bearing link pointing at an address the attacker chose. Per the second corollary, the
exception is written at the point of call in `bootstrap/app.php`, where a reader meets it,
rather than left to this document.

Trusted proxies was classified in the same category during the originating session and the
classification was wrong. That middleware has no environment gate, runs under test, and is
provable at the request seam the suite already has. The analogy was the error, not the
reading, and the category is one member rather than two.

## Considered options

**A line in the agent instruction index pointing at a document.** Cheaper, and it states only
the *what*. The *why* — that the guard's branches have to be observable — is what allows a new
case to be decided, and it is exactly what the short form drops.

**`nunomaduro/essentials` as a dependency.** The package answers the same question and bundles
nine configurables behind one install. Three of them are already here and identical in
substance. It also ships three Artisan commands, two of which publish formatter and refactorer
configurations that this repository already has and tunes harder, per
[ADR-0001](0001-enforced-quality-gate-over-upstream-fidelity.md); the commands cannot be
removed selectively, because suppressing discovery of the package's provider suppresses all
nine configurables with it, so the dependency would be paid in order to disable half of what
it brings. The seam it would occupy already exists — the provider's private configuration
method is exactly where its calls would go — and each configurable is a single framework call,
which is a poor ratio of dependency surface to code saved for a repository whose argument is
security. The honest cost of refusing it is recorded rather than buried: package code would
not be in the coverage perimeter, which is the application namespace alone, so it would escape
both the full-coverage minimum and the mutation instrument. Repatriating means every line
added is subject to both. That is work, and it is the work this repository exists to do.

**Placing each default where the reference package places it.** Rejected by the case above,
and by two defaults refused outright. `Model::automaticallyEagerLoadRelationships()` is a
performance change obtained by altering the query semantics of the whole application, which is
outside an effort scoped to quality and security — and it cancels the strict-model decision it
would sit beside: relationship autoloading is attempted *before* the lazy-loading guard is
consulted, and the autoload callback is registered on every collection the framework hydrates,
so the lazy-loading violation never fires in exactly the case it exists to catch. Aggressive
asset prefetching is refused on the same criterion — delivery performance, neither quality nor
security — and on a second one specific to here: the build output is not committed and the
aggregate command does not build, so no feature test can render the asset directive during a
gate run. It is the one candidate for which no acceptable location exists, since the only
place its effect is observable is the browser suite, which the measurement excludes.

## Consequences

A contributor installing the next framework default has a question to answer before choosing a
file, and the answer is sometimes the test bootstrap rather than the provider. What is costly
to reverse is not the seven calls but the rule, once it has been followed across twenty
defaults.

The rule can send a default out of the application namespace, and everything outside that
namespace is outside the coverage and mutation perimeters. That is a saving where the guard
genuinely disappears, and it would be an escape hatch if it were used to relocate a guard that
was merely inconvenient to prove. The corollary is narrow on purpose: it applies where no test
can observe the other branch, not where writing the test is tedious.

The third corollary keeps a category alive that this repository would otherwise not have — a
behaviour it has decided and cannot demonstrate. Its cost is that the category has to stay
short and enumerated. It has one member, named above, and adding a second is a decision rather
than a placement.

Nothing enforces the rule. It is prose, in a repository whose thesis is that prose does not
constrain agents. What can be enforced was: each provable guard is pinned by two cases, one
per branch, and each test-bootstrap default is asserted by an ordinary test so that deleting
the hook fails the suite instead of removing a protection in silence. The rule itself is
carried by this record and by the index that points at it.
