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

**Status:** resolved

- [x] The Pest browser plugin is a development dependency
- [x] The browser binaries it drives are installed by a documented command rather than assumed present
- [x] Browser tests live in their own suite, separate from the unit and feature suites
- [x] A dedicated command runs that suite and only that suite
- [x] The command is **not** part of the aggregate command and is not run by the pipeline
- [x] The aggregate command's test run does not execute the browser suite, so no browser scenario contributes to the coverage minimum
- [x] Every browser test asserts that nothing was written to the browser console
- [x] The success path of each navigation the application offers is exercised end to end
- [x] Browser tests are held to the same standard as the rest of the tree: functional style, strict types, analysed and formatted
- [x] No suppression, no architecture exemption and no lowered coverage threshold is introduced to accommodate the new suite
- [x] No scheduled or periodic workflow is added
- [x] `composer test` passes, and its runtime is unchanged
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

**The two commands are `composer browser:install` and `composer browser:test`, and they are
documented in the quality-gate skill beside the gate rather than inside it.** The first
delegates to an npm script that downloads Chromium alone — 95 MiB, against roughly half a
gigabyte for the three engines Playwright installs by default — which is the honest minimum for
a suite the spec puts no cross-browser matrix in. The Playwright npm package itself is a
`devDependency` and so is pinned in `package-lock.json` and installed by `composer setup`; only
the binary is left to the on-demand command. Nothing in the pipeline runs either one.

**The exclusion from coverage is by suite name and was verified to be load-bearing.**
`phpunit.xml` gains a third `Browser` suite, and the gate's Pest invocation gains
`--testsuite=Unit,Feature`. The same runner without those names collects twelve tests instead of
eleven, so the flag is what does the work rather than the directory layout. The gate's numbers
are unchanged by the whole ticket — eleven tests, sixty-eight assertions, a hundred per cent —
which is the statement that no browser scenario reached the measurement.

**`composer mutate` gained the same suite names, and that is this ticket's one edit to ticket
10's work.** Ticket 10 landed while this branch was open, and its command names no suites — so
the moment a third suite existed, `composer mutate` would have driven the browser once per
mutant, and `--covered-only` would have accepted a line as a mutation target on the strength of
a browser scenario having walked past it. That is the same substitution the coverage minimum is
protected from, arriving by the other door. Measured on today's tree the flag changes nothing:
25 mutants and 72.00 per cent with it and without it, because every application line is already
covered by the unit and feature suites, so the browser scenario qualifies no line that was not
already qualified. It is added for the tree that has a line only a browser reaches, which is the
tree the spec's twenty-ninth user story is written about. Ticket 10's measurement and its
`--min=72` are untouched.

**Runtime is unchanged, measured rather than assumed.** Three timed runs of the full gate before
the change: 10.86s, 8.28s, 9.00s. Three after: 12.47s, 10.82s, 8.97s. The ranges overlap and the
difference is inside this machine's run-to-run variance; the only work the gate genuinely gains
is the annotation check reading one more file, 33 to 34.

**The console assertion was probed for discriminating power, and it sees less than its name
suggests.** The plugin captures the console by replacing `console.log` on the page and by
listening for the window's `error` event. Five defects were each added to the view in turn and
the suite observed: a `console.log` fails it, an uncaught exception fails it, and
`console.error`, `console.warn` and a 404 on a `<script src>` or a `<link rel=stylesheet>` all
pass. So of the three defect classes this ticket names, two are caught — a template that throws
once the browser executes it, and a script that fails to boot — and the third, an asset the
build did not emit, is not. That is recorded in the spec rather than repaired: repairing it
means writing a second console capture alongside the plugin's, which is a fixer beside the owner
of a concern and the arrangement the ownership rule exists to prevent. The plugin's own
`assertNoBrokenImages` covers one slice of the gap and is left out because that slice is empty —
the shipped view declares no `img` element, so the assertion would report a guarantee it never
exercised. All of this is stated so the suite is not credited with something it does not
provide.

**`composer browser:test` builds the frontend assets before it runs, which answers one of the
spec's two open unknowns in the negative while keeping its premise.** The suite passes on an
unbuilt tree, because the shipped view falls back to an inlined stylesheet when no build
manifest exists — but on an unbuilt tree the `@vite` directive never runs and the application's
script is never requested, so the scenarios exercise a fallback branch rather than the page a
user gets. Folding the build into the command costs about a second and keeps the prerequisite
list at one download, which is what the spec asked for when it worried about turning "run this
command" into "run these four".

**The other open unknown — whether the plugin's dependency resolution fits the point where Pest,
PHPUnit and `laravel/pao` meet — turned out to be a non-event.** The plugin resolved and
installed against the existing lock without moving any of the three, and the gate was green
immediately afterwards. The point was roomier than the migration ticket's experience suggested.

**One scenario, because the application offers one navigation.** It asserts the console first
and the page's content second, which is the order the ticket asks for. A first draft carried two
— the same route reached once by path and once by name — and the second was removed on review as
a duplicate rather than a second navigation: `route('home')` resolves to `/`, so it drove the
same page through the same assertions. The value here is a harness ready for the tenth route,
and two copies of one scenario make a worse harness than one, not a better one.

**The plugin writes a screenshot beside each failing scenario, and `tests/Browser/Screenshots`
is gitignored.** That is generated debugging output about a run, in the same class as
`public/build` — not a fixture the tests compare against. It is named here because the spec's
prohibition on escape hatches ends with "no added ignore paths" and this is, literally, an added
ignore path.

It is not one of the kind that prohibition means — those are tool perimeters, `pint.json`,
`.prettierignore`, PHPStan's, where an entry removes existing code from a check — but the
distinction is thinner than it first looks and the reason is worth recording. Pint, Rector and
Larastan each carry their own perimeter and none of them reads `.gitignore`. The annotation
check does, by construction: it enumerates the tree with `git ls-files --exclude-standard`, so
an ignored path is a path it does not scan. A `.php` file placed under
`tests/Browser/Screenshots` would therefore escape it.

That residual hole is left open rather than closed. Nothing writes PHP there — the plugin writes
PNGs — and reaching the hole means putting a source file in a screenshots directory that no
autoloader maps and no suite loads, which is a deliberate act rather than the inattention the
rule exists to catch. It is the same shape as the narrowing the route-closure assertion already
accepts, and it is stated on the same terms.
