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

**Status:** ready-for-agent

- [ ] A test in the blocking suite walks the Primitive directory and fails on any forbidden pattern, naming the file and the pattern
- [ ] The forbidden list covers the application namespace, facades, and the authentication, request, session, configuration, route and old-input helpers
- [ ] The translation helper is permitted by that test and used by nothing in this effort
- [ ] The button Primitive exposes the primary and secondary variants at one size, and no others
- [ ] Its variant table lives inline in the template, and no test asserts which classes a variant emits
- [ ] The copy control is Chrome rather than a Primitive, and its behaviour needs no new dependency
- [ ] The hero renders one headline in the display face, one supporting line in the reading face, and one pair of calls to action
- [ ] The primary call to action copies the installation command and reports success visibly
- [ ] The secondary call to action links to the repository
- [ ] The HTTP seam asserts the hero's load-bearing content
- [ ] The browser suite observes the copy control reporting success
- [ ] The contrast test and the accessibility audit still pass
- [ ] `composer test` passes
- [ ] `composer browser:test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
