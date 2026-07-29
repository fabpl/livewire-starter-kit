# 03 — The hero, and the guard on Primitives

**What to build:** the Page gets its first block of real content — one headline, one supporting
line, one pair of calls to action — and with it the first Primitive and the mechanism that keeps
Primitives honest.

The guard arrives in this ticket rather than in ticket 01, and the reason is that a traversal
over a directory that does not yet exist passes for the wrong reason. A test that has never seen
a file it could reject is not a guard, it is a placeholder. It is written here, against the first
Primitive it actually constrains.

The rule it enforces is the one [ADR-0003](../../../docs/adr/0003-livewire-pages-are-classes-ui-primitives-are-templates.md)
grants the exemption for: a Primitive is a pure function of its props and its slots. It names no
application symbol and reaches no ambient state — no application namespace, no facade, and none
of the helpers that read authentication, the request, the session, configuration, the route
table or old input. The translation helper is the single allowance, permitted so that a
consuming project can introduce translation without reopening the rule; nothing in this effort
uses it. The test walks the Primitive directory, matches an explicit list of forbidden patterns,
and fails naming both the file and the pattern — the shape the routing test already uses to
forbid route closures by traversal rather than by instruction.

The rule earns more than safety. A component that knows only its props is portable by
construction, which is the promise the shadcn idiom makes in prose and this one keeps as a
verified property.

The button Primitive exposes only what this composition renders: the primary and secondary
variants, at one size. The outline variant and the small and large sizes are not built. This is
not economy for its own sake — the seam that would verify them is the Page that renders them,
and building variants the Page does not render would mean either shipping unverified code or
inventing a demonstration view whose only consumer is a test. Its variant table lives inline in
the template, which is the exemption ADR-0003 grants, and which exists because coverage and
mutation applied to a mapping from a variant name to a string of classes produce the
implementation restated in a test file.

Consumer overrides are constrained by convention rather than by a dependency. The framework's
attribute merge concatenates, so a utility passed by a caller does not reliably beat the
component's own — the winner is stylesheet order, not intent. A PHP port of the JavaScript
class-merging library exists and is refused, because it is a third-party reimplementation of the
framework's conflict semantics that has to be kept correct for the styling to be correct. The
rule instead: the class attribute of a Primitive carries positioning and spacing utilities only.
What must vary is a variant, and a missing variant is added rather than worked around.

The calls to action do something, and that is a correctness requirement rather than a flourish.
This repository has one route, so two buttons leading nowhere on the page that argues for rigour
would be a decorative lie. The primary copies the installation command to the clipboard and
reports success visibly; the secondary links to the repository. The copy control is Chrome, not
a Primitive — its presentation is built for that content — and its behaviour is Alpine, which
arrives with Livewire and needs no dependency.

Typography follows the design document: the headline in the display face, the supporting line in
the reading face, which is the one place on this page the reading face is opt-in *into*. The
copy is English, and it says what this repository is rather than what a starter kit generally
is.

The browser assertion for the copy control should be made on the visible confirmation rather
than on the clipboard's contents, unless reading the clipboard proves straightforward — the
behaviour under test is that the reader is told it worked.

**Parent:** [spec.md](../spec.md)

**Blocked by:** 02

**Status:** resolved

- [x] A test in the blocking suite walks the Primitive directory and fails on any forbidden pattern, naming the file and the pattern
- [x] The forbidden list covers the application namespace, facades, and the authentication, request, session, configuration, route and old-input helpers — after a repair, see the first comment
- [x] The translation helper is permitted by that test and used by nothing in this effort
- [x] The button Primitive exposes the primary and secondary variants at one size, and no others
- [x] Its variant table lives inline in the template, and no test asserts which classes a variant emits
- [x] The copy control is Chrome rather than a Primitive, and its behaviour needs no new dependency
- [x] The hero renders one headline in the display face, one supporting line in the reading face, and one pair of calls to action
- [x] The primary call to action copies the installation command and reports success visibly
- [x] The secondary call to action links to the repository
- [x] The HTTP seam asserts the hero's load-bearing content
- [x] The browser suite observes the copy control reporting success — with one grant to the harness, see the second comment
- [x] The contrast test and the accessibility audit still pass
- [x] `composer test` passes
- [x] `composer browser:test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

**The guard was written green over the construct it forbids, and needed a second test to say
so.** The first draft of the facade rule excluded a leading backslash from its lookbehind, so
that a fully-qualified application class would not be reported twice. The effect was that
`\Illuminate\Support\Facades\Auth::user()` — the ordinary way to reach a facade from a Blade
template — matched no rule at all. Probing by hand had missed it, because the probe file also
carried an unqualified `Auth::user()` and the run could not say which line had fired which rule.

The ticket's own argument is why this mattered: a traversal that has never seen a file it could
reject is a placeholder. The traversal had files, and still could not reject the thing it names.
So the fix was not only to widen the lookbehind. Each rule now carries a specimen of the
construct it forbids, in one table so that a rule cannot be added without one, and a second test
fires every rule at its specimen. A rule that cannot match is now a failure rather than a silence.
Two narrower escapes were closed in the same pass: `preg_match` returning `false` was being read
as a clean file, and the non-empty assertion counted any file rather than a Primitive.

**The clipboard needed a permission the harness withholds and a real browser grants.** Playwright's
Chromium refuses `clipboard-write` by default; Chrome grants it to the focused document without
prompting. Left at the default, `writeText` rejects with `NotAllowedError` and the test fails on a
control that works everywhere a reader would meet it, so the permission is granted to the browser
context — the harness is moved towards the browser, not the page towards the harness.

Measured while diagnosing it, and worth knowing before trusting the browser suite too far: that
rejection reaches **neither** `assertNoConsoleLogs` **nor** `assertNoJavaScriptErrors`. An
unhandled promise rejection is invisible to both. The assertion on the visible confirmation is
therefore the only thing standing between a broken copy control and a green suite, which is a
stronger reason for the ticket's instruction to assert the confirmation than the one it gave.

**The hero's supporting line dropped a claim the repository does not back.** It was drafted as
"static analysis, full coverage, mutation testing, formatting and refactoring — held by one
command the pipeline runs unchanged". Mutation testing is not held by that command: `composer
mutate` is a separate script and `.github/workflows/tests.yml` runs `composer ci:check`, which is
`@test`. The claim was removed rather than softened. Ticket 05 states the score on its own terms.

**A fourth face appears, and it is not a typeface decision.** The command is set in `font-mono`,
which resolves to the platform's fixed-width stack. Nothing is downloaded, so none of the
reproducibility or preloading arguments behind the three self-hosted faces apply and no
`--font-mono` Token is declared; what it buys is that a shell command reads as one. Recorded here
because "three typefaces" is otherwise a true sentence about this page that a reader would find
contradicted by the screen.

**Hover and focus are drawn in the focus Token rather than by softening the fill.** `--primary`
was already moved once, per ADR-0004, to bring its white label to 4.58:1 — barely over, with
white already at its maximum. Every way Tailwind offers to soften a fill on hover composites it
against the page, which on the parchment stage lightens it and drops that label under 4.5:1; a
brightness filter dims the label alongside it and lands at 4.46:1. So the fill does not move, and
both affordances use `--ring`, which `ContrastTest` already holds at 3:1 against the stage.
