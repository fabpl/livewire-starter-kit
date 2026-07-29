# Spec: Home redesign and design-system foundation

**Status:** ready-for-agent

## Problem Statement

The repository serves one route, and what it serves is the upstream kit's welcome page. That
page says "Let's get started" and talks about Laravel. It does not mention the thing this
repository actually is — a foundation whose argument is a quality gate nothing can weaken —
so the only page the project renders is the only place its own argument is absent.

Behind that gap sits a larger one. There is no Page, no Primitive, no Chrome, no Token: no
convention for what a page is, no component layer, no design vocabulary, no light and dark
narrative, no accessibility bar. An agent asked to build the first screen has to invent all
of it, and will invent it differently in each project that starts here. The quality-gate
effort deferred exactly this, listing *"any user-interface component layer"* among the things
it would not touch and naming it *"the first decision to revisit"* once the application grew
a real interface. That deferral expires now.

The gap has a second edge that is easy to miss. The gate reads `app/`, `bin/`, `bootstrap/`,
`config/`, `database/`, `public/`, `routes/` and `tests/`. It does not read `resources/`.
Every convention introduced by an interface layer therefore lands, by default, in the one
part of the tree where nothing holds it — which is how a repository built on mechanical
enforcement acquires a large unenforced region without anyone deciding to.

Finally, the theme the project wants is not safe as published. The "Claude" theme by tweakcn
declares three pairings that fail WCAG 2.2 level AA, and two of them are the most repeated
surfaces of any design system: every piece of secondary text, and the label of the primary
button. Adopting the theme unexamined would propagate those two failures into every project
built from this foundation.

## Solution

The home becomes a Page — a full-page Livewire component — that presents this kit in its own
terms, and under it lands the minimum design system that page needs and nothing more.

The design system is shadcn's token contract, valued by tweakcn's "Claude" theme, carried in
two layers of CSS custom properties so a theme can be swapped by replacing one block. Three
typefaces arrive as locked npm dependencies rather than a build-time fetch. Light and dark
are both real, selected by a three-state control that can still defer to the operating
system. Two Primitives and four pieces of Chrome exist, because the composition needs
exactly those.

What makes this more than a coat of paint is that every rule it introduces is held by a
mechanism rather than by prose:

- Colour contrast is arithmetic over the declared tokens, in the blocking suite, in both
  themes.
- The purity of the Primitive directory — the property that makes those components portable
  — is a traversal in the blocking suite.
- The rendered page is asserted at the existing HTTP seam, and the Primitives are asserted
  through it rather than in isolation.
- No Livewire component may sit at the root of its namespace, held by an architecture test.

Two decisions were costly enough to record before the work began, and are already written:
[ADR-0003](../../docs/adr/0003-livewire-pages-are-classes-ui-primitives-are-templates.md)
places Page PHP inside the gate and exempts Primitive templates from it behind a mechanical
guard; [ADR-0004](../../docs/adr/0004-contrast-outranks-fidelity-to-the-published-theme.md)
settles that level AA outranks fidelity to the published theme.

## User Stories

1. As a developer starting a project from this kit, I want the home page to describe what the
   kit guarantees, so that I can tell at a glance what I have inherited.
2. As a developer starting a project from this kit, I want a Page to already exist, so that
   the shape of the next one is copied rather than invented.
3. As an agent asked to add a screen, I want one unambiguous location for a Page's PHP, so
   that I do not have to choose between two idioms the framework both supports.
4. As an agent asked to add a screen, I want the Page's view to be found without my writing a
   `render` method, so that the mapping is stated once by configuration rather than repeated
   per component.
5. As a maintainer, I want a Page's PHP held by the same analysers as the rest of the
   application, so that the interface layer is not a region where the strictness stops.
6. As a maintainer, I want a Primitive to be unable to reach into the application, so that
   copying it into another project cannot silently take a dependency with it.
7. As a maintainer, I want that constraint enforced by a test rather than by a paragraph, so
   that it survives contributors who never read the paragraph.
8. As an agent writing a Primitive, I want to express variants in the template, so that the
   suite is not filled with tests restating which classes a variant emits.
9. As a designer, I want the palette declared once as Tokens, so that changing a colour is one
   edit rather than a search across templates.
10. As a designer, I want the theme swappable by replacing one block of declarations, so that
    a different tweakcn theme can be dropped in without touching component markup.
11. As a reader with low vision, I want body and secondary text to meet AA contrast, so that I
    can read the page without assistive magnification.
12. As a reader with low vision, I want the primary button's label to meet AA contrast, so
    that the most important control on the page is also the most legible.
13. As a keyboard user, I want a focus indicator that stands out against both themes, so that
    I never lose track of where I am.
14. As a maintainer, I want contrast checked in the blocking command, so that a colour change
    that breaks legibility cannot be merged.
15. As a maintainer, I want a newly added foreground Token to fail the suite until it is
    paired with a background, so that the check cannot be escaped by omission.
16. As a visitor, I want the page rendered on a warm parchment stage rather than pure white,
    so that the theme's own character is visible rather than described.
17. As a visitor who prefers dark interfaces, I want the page to follow my system setting on
    first visit, so that I am not flashed with a bright page.
18. As a visitor, I want to override that setting, so that I can read the page in the mode I
    want regardless of my system.
19. As a visitor who has overridden it, I want to return to following my system, so that the
    override is not a one-way door.
20. As a visitor, I want my choice remembered between visits, so that I do not set it again
    on every page load.
21. As a visitor, I want no flash of the wrong theme while the page loads, so that the
    experience is not visibly assembled in front of me.
22. As a visitor on a slow connection, I want the typefaces self-hosted and preloaded, so that
    text does not reflow noticeably after the page appears.
23. As a maintainer, I want the typefaces resolved from locked dependencies, so that building
    the same commit twice cannot produce different bytes.
24. As a maintainer, I want the build to need no third-party HTTP request, so that the browser
    suite's build step does not depend on someone else's uptime.
25. As a visitor, I want the call to action to actually do something, so that the page that
    argues for rigour is not itself a decorative mock-up.
26. As a developer evaluating the kit, I want to copy the installation command in one click,
    so that I can try it without retyping.
27. As a developer evaluating the kit, I want a visible confirmation that the command was
    copied, so that I know the click registered.
28. As a developer evaluating the kit, I want a link to the repository, so that I can read the
    source before installing anything.
29. As a maintainer, I want the claims on the page to be facts drawn from this repository, so
    that the page cannot drift into marketing that the tooling does not back.
30. As a maintainer, I want the layout to carry the document and nothing else, so that the
    first authenticated screen can bring its own shell without unpicking this one.
31. As an agent, I want the vocabulary of Token, Primitive, Chrome and Page to mean one thing
    each, so that a ticket naming one of them is unambiguous.
32. As a maintainer, I want the upstream welcome page and its inline stylesheet removed, so
    that no second, silently stale source of styling survives.
33. As a maintainer, I want the browser suite to audit roles, labels and heading order, so
    that the accessibility of the page is checked where arithmetic cannot reach.
34. As a maintainer, I want the browser suite to observe the theme switch end to end, so that
    the one piece of hand-written JavaScript in the tree is not unobserved.
35. As a visitor using a screen reader, I want the theme control to announce what it does and
    which state is active, so that it is operable without sight.

## Implementation Decisions

### The token layer

Two layers of CSS custom properties, as shadcn does it. A block of raw Tokens under `:root`,
overridden under a dark selector, and a `@theme inline` block mapping them onto Tailwind's
names. The `inline` keyword is load-bearing: without it Tailwind freezes the value at compile
time and a utility no longer follows a redefinition in the dark scope; with it the utility
emits a `var()` reference and switching themes is plain cascade.

Values stay in the hexadecimal notation the design document uses. shadcn publishes canonical
themes in OKLCH, but the source of truth here is the design document, and a converted value
is one nobody can check by eye against the specification. Nothing is lost: Tailwind 4 mixes
in `oklab` for opacity modifiers whatever notation the input uses.

The declared set is the shadcn contract in full. Chart, sidebar and popover Tokens are
declared although this effort renders none of them — a Token vocabulary is fixed upstream
rather than invented here, it costs only CSS, and splitting it would make the block
undiffable against its source.

An earlier draft of this section drew the line differently: it kept chart and sidebar but
withheld popover, on the grounds that the day a component needs a popover surface is the day
its value gets decided against a real one. That line did not survive contact with the
implementation, because it is not a line — all three are equally unrendered, and the
argument for declaring the first two is the argument for declaring the third. Declaring the
contract whole is what makes the block diffable against its source and spares a consuming
project inventing the names this one left out. Every surface in the contract carries a
foreground; the chart Tokens carry none, because they colour marks rather than text, and
that absence is the contract's own shape rather than an omission to repair.

### Colour values move where contrast requires it

Per [ADR-0004](../../docs/adr/0004-contrast-outranks-fidelity-to-the-published-theme.md),
a pairing that fails its threshold has its Token moved until it passes, and the divergence
from the published theme is recorded next to the value. Three pairings are known to fail
before the work starts, and they are the reason the rule exists rather than incidental
findings: secondary text on the page stage, white on the primary fill in both themes, and the
input border against the stage. Any further failure the test surfaces is treated the same way.

The dark scope declares only what it needs to. A Token with no dark value inherits its light
one, and is derived explicitly **only** where the contrast test fails on the inherited pair.
The mechanism decides, not taste. Two consequences are known in advance: the focus ring
inherits unchanged, clearing its threshold on both stages; the accent foreground must be
derived, because inherited it lands as a near-black on a near-black.

### Radius, spacing and elevation

Spacing declares nothing. The theme's eight steps are all multiples of four pixels and
therefore already exactly expressible on Tailwind's default scale; declaring named spacing
Tokens would create a second vocabulary for values the first one already names, and the first
disagreement between the two would be a layout defect nobody could read.

Elevation declares nothing either. The theme declares no shadows, and the design document's
prose description of them is the definition of Tailwind's own small shadow.

Radius declares, because Tailwind's scale is offset by one step from the theme's and the
design document prescribes control corners by Tailwind's *name*. A single root radius plus
the shadcn calculation chain reproduces the theme's five values exactly, which keeps one knob
for retuning the whole scale and keeps the shadcn contract intact:

```css
--radius: 0.5rem;
--radius-xs: calc(var(--radius) - 4px);  /*  4px */
--radius-sm: calc(var(--radius) - 2px);  /*  6px */
--radius-md: var(--radius);              /*  8px */
--radius-lg: calc(var(--radius) + 2px);  /* 10px */
--radius-xl: calc(var(--radius) + 4px);  /* 12px */
```

The accepted cost: the medium radius no longer means what Tailwind's documentation says it
means. That is the price of following the design document's own component instructions, and
it is what shadcn itself does.

### Typography and font delivery

Three families, and only the weights the design document specifies: the display face at
semibold, the interface face at regular and medium, the reading face at regular. Latin subset
only, swap display, no italic — the composition has none.

Delivery moves to the Fontsource provider. The Vite plugin's providers differ in one way that
matters here: the two remote providers fetch from a third party's stylesheet endpoint at build
time, while Fontsource resolves from an installed package under `node_modules`. Fonts thereby
become versioned dependencies fixed by the lockfile and fetched by the install step the chain
already runs, rather than an unversioned HTTP fetch repeated on every build. That is the
reproducibility argument of
[ADR-0001](../../docs/adr/0001-enforced-quality-gate-over-upstream-fidelity.md) applied to
assets, and it removes a network dependency from the browser suite's build step.

Preloading is restricted rather than left at the plugin's default of every variant: the three
faces that set above-the-fold text are preloaded, and the interface medium weight is left to
arrive by swap, since it sets only short labels.

The interface face is the page default. The design document holds two statements in tension —
the reading face is the theme's signature texture, and dense chrome must not be set in it —
and making the reading face opt-in is what renders the forbidden mistake impossible by
default. On this composition the reading passages are few and identifiable. Three semantic
font Tokens are mapped: display, sans, serif.

### Where the Page lives

Per [ADR-0003](../../docs/adr/0003-livewire-pages-are-classes-ui-primitives-are-templates.md),
a Page is a class-based Livewire component in a `Pages` namespace under the application's
Livewire root, and its view is resolved implicitly. Livewire's configuration is published so
that the view root becomes the views directory itself rather than a `livewire` subdirectory,
which makes a component's view mirror its class path without a `render` method. The `pages`
entry is removed from the component namespaces, because that entry binds the same directory to
the view-based resolver this repository has just refused; one path, one resolver.

Because the mirror starts at the root of the views directory, a Livewire component at the root
of its namespace would write its view among the ordinary views. An architecture test forbids
it, so the rule is held rather than remembered.

The route uses Livewire's own routing macro and passes the **class**, not the component's dot
name. The macro accepts either; a class reference is checked by the analyser, and a string is a
reference nothing verifies. The macro expands to a controller action, so the existing
prohibition on route closures is unaffected. The route keeps the name it already has.

The Page class has no body: no `render`, because resolution is implicit, and no properties,
because it holds no state and is forbidden from inventing any to justify itself. A theme
switch is client state and does not travel to the server for a round trip.

### The layout

One layout, and it carries the document only: the HTML shell, the head, the font directive,
the bundle, the theme class hook, and the slot. The header and footer of this page are Chrome
composed by the Page.

The reasoning is that the repository has one page, so anything placed in the layout is a
conjecture about pages that do not exist — and this particular conjecture is already
refutable. This composition wants a marketing bar; the first authenticated screen of a real
project will want a different shell entirely. Two chromes. Fusing them into the single global
layout would force the first real consumer to unpick the kit's layout before using it.

### The Primitives

Two, and only the variants this composition renders. `ui/button` exposes the primary,
secondary and ghost variants at one size; the outline variant and the small and large sizes are
not built, because nothing renders them and the seam that would test them would be a view whose
only consumer is a test. `ui/card` exposes no variants.

Both are anonymous Blade templates and may hold their variant tables inline. The bounding rule,
which the guard enforces: a Primitive is a pure function of its props and slots. It names no
application symbol and reaches no ambient state — no application namespace, no facade, no
authentication, request, session, configuration, route or old-input helper. The translation
helper is the single allowance, permitted so a consuming project can introduce translation
without reopening the rule; this effort does not use it.

Consumer overrides are constrained by convention rather than by a dependency. Laravel's
attribute merge concatenates, so a utility passed by a caller does not reliably beat the
component's own — the winner is stylesheet order, not intent. A PHP port of the JavaScript
class-merging library exists and is refused: it is a third-party reimplementation of Tailwind's
conflict semantics that must be kept correct for the styling to be correct. The rule instead:
the class attribute of a Primitive carries positioning and spacing utilities only. What must
vary is a variant, and a missing variant is added rather than worked around.

### The Chrome and dark mode

Four Chrome components: header, footer, theme control, installation command. They are Chrome
rather than Primitives because they are specific to this kit and not portable; the theme
control in particular holds client state, which the purity rule permits but which is not the
shape of a Primitive.

Dark mode is selected by a class on the document element, declared to Tailwind as a custom
variant, because the framework's default binds the dark variant to the media query alone and
that leaves no room for an override. Three states: light, dark, and system. System is a state
of its own, not the absence of one — a two-state control destroys the system link on the first
click with no way back. The choice persists in local storage, and while the system state is
active a media-query listener keeps the page following the operating system.

A small script in the head applies the class before the first paint. It is the only way to
avoid a flash of the wrong theme, since a deferred module paints first. It reads local storage,
reads the media query, sets a class, and is forbidden from growing beyond that.

### The composition of the home

Four blocks, one composition per viewport, medium-low density, a readable measure for prose
and a wider shell for chrome, near-white cards on the parchment stage.

A quiet bar carrying the kit's name and a repository link — chrome coloured as chrome, with no
primary fill. A hero with one headline, one supporting line and one pair of calls to action. A section of three cards stating what the gate guarantees, drawn from this
repository's own configuration: the analysis level and the two rule sets beyond it, the
coverage minimum and the mutation score, and formatting and refactoring with no suppression
mechanism anywhere. A footer with the licence, the repository link and the theme control.

The calls to action do something, which is a correctness requirement rather than a flourish:
this repository has one route, so two buttons leading nowhere on the page that argues for
rigour would be a decorative lie. The primary copies the installation command to the clipboard
with visible confirmation; the secondary links to the repository.

Copy is written in English. Every other artefact in this repository — the package description,
the decision records, the specs, the test names — is in English, and a foundation meant to be
adopted by others is too. Translation files are not introduced: a translation layer for one
locale and a dozen strings is indirection with no second case to justify it.

### What is removed

The upstream welcome view goes, along with the inline stylesheet it carries as a fallback for
a missing build. That fallback would have to be regenerated by hand on every theme change — a
second source of truth for styling, condemned to disagree with the first. The browser suite
already builds before it runs, so the page assumes a build exists.

The interface font declaration currently in the stylesheet is replaced, and the single remote
font definition in the build configuration is replaced by the three local ones.

## Testing Decisions

### What makes a good test here

A test asserts something this repository decided, at the outermost point where the decision is
observable, and it asserts a property rather than a transcript. The two sharp edges in this
effort:

Contrast is asserted against the **standard's thresholds**, never against an expected ratio. A
pinned ratio is a change detector that reddens on every adjustment of hue and has never caught
a defect.

Primitives are asserted **through the Page that renders them**, never in isolation. This is the
highest available seam, and it is also the only honest one: Blaze folds a Primitive into its
parent, so a Primitive rendered alone is an artefact that does not exist at runtime.

Neither of the two static tests restates configuration. The colours are declared once, in the
stylesheet; the thresholds come from WCAG; the forbidden patterns are the rule itself, not a
copy of something declared elsewhere. This is what separates them from the class of test the
quality-gate spec refused — one that reads a configuration file and restates what it says, so
that the two can drift.

### Seams

Three, one of them new.

**The HTTP seam, blocking.** The Page responds and its markup carries what the composition
promises. Everything about the rendered page that must block goes here, Primitives included.
The existing example feature test is absorbed rather than kept: it already asserts that the
root route responds, without naming what it is verifying.

**The frontend source seam, blocking, new.** The suite reads the frontend source tree: the
stylesheet for contrast, the Primitive directory for purity. New because no existing seam
reaches it — resolving CSS custom properties requires a browser, and the browser suite does not
block. Its shape has an exact precedent in the routing test, which walks the route table and
asserts an invariant rather than exercising a behaviour.

**The browser seam, not blocking.** Console hygiene, the accessibility audit, and the theme
switch observed end to end.

A fourth seam was considered and refused: a demonstration view rendering Primitive variants the
application does not use. It would be production code whose only consumer is a test, and it is
the reason the button's variant matrix is limited to what the composition renders.

### Depth of proof

The contrast test computes the ratio of each declared pairing in both themes and compares it to
4.5:1 for text and 3:1 for component boundaries and focus indicators. Pairings — which
foreground sits on which background — are declared in the test, because the stylesheet carries
values and not semantics; no colour value is written in the test. A foreground Token appearing
in no pairing fails, so a Token added later cannot escape by being forgotten.

The purity test walks the Primitive directory, matches an explicit list of forbidden patterns,
and fails naming both the file and the pattern.

The architecture test asserts that no Livewire component sits at the root of its namespace.

At the HTTP seam: the Page responds, and the composition's load-bearing content is present.

In the browser suite: no console output and no JavaScript errors, as today; the accessibility
audit at serious impact and above; and the theme switch across its three states, including
persistence.

The Page class contributes nothing to coverage or mutation, having no executable body. This is
a property of the decision rather than a happy accident: a Page holds no state, so there is
nothing in it to cover.

### Not verified

Focus visibility, target size, motion preferences and reading order are not held by the
blocking suite. The accessibility audit reaches some of them, and it does not block — a
boundary inherited from the quality-gate effort and not reopened here.

Nothing asserts that a Primitive's variant emits particular classes. That is the exemption
[ADR-0003](../../docs/adr/0003-livewire-pages-are-classes-ui-primitives-are-templates.md)
grants, and the reason for it is that such an assertion is the implementation restated.

Nothing asserts that the typefaces resolve without network access. The property follows from
the provider's resolution strategy rather than from a test, and asserting it would mean
running a build inside the blocking suite.

Nothing asserts the visual result. There is no screenshot comparison, and there is no
responsive matrix.

### Prior art

The routing test is the pattern for both static tests: an invariant asserted by traversal, with
the reason for the traversal written at the point it happens. The strict-models test is the
pattern for stating in a note which effects are pinned and which are taken on the framework's
word. The existing browser test is the pattern for the browser suite's shape, and is renamed
rather than duplicated.

## Out of Scope

- Any Primitive the composition does not render: no input, dialog, dropdown, table, sidebar or
  chart. Their Tokens exist; their components do not.
- The button variants and sizes this composition does not render.
- Tokens beyond the shadcn contract. The contract is declared whole; nothing is added to it.
- A shared variant engine. One table per Primitive until a third one proves the shape.
- A class-merging dependency for consumer overrides.
- Translation files, and any second locale.
- Making the browser suite part of the blocking gate.
- Screenshot comparison, a responsive matrix, and cross-browser testing.
- Authentication, and any screen behind it.
- A second Page, and the application shell a second Page would want.
- Changing the coverage minimum, the mutation threshold, or any tool's configured strictness.

## Further Notes

The mutation threshold deserves a look during implementation rather than a prediction now.
This effort adds almost no mutable PHP: the Page has no body, and the variant tables live
outside the measured perimeter by design. The score may therefore move without anyone having
decided to move it. Observe it, and if it moves, say so rather than adjusting the threshold —
per the quality-gate skill, the bar is never weakened to make it pass.

The two static tests are the first in this repository to treat the frontend source tree as an
object of assertion. If a third such test appears, that is the moment to ask whether they want
a shared reader rather than three parsers.

The glossary this effort introduced reverses a refusal made by the quality-gate spec, which
declined a project glossary on the grounds that there was no domain — only tooling. The
condition that refusal was waiting on is now met: Token, Primitive, Chrome and Page name
categories the suite can tell apart.
