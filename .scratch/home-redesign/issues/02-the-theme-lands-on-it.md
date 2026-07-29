# 02 — The theme lands on it

**What to build:** the Page from ticket 01 acquires its stage. The palette, the corner radii
and the three typefaces arrive as Tokens; light and dark both become real, with dark following
the reader's system on first visit; and the two mechanisms that will hold every later ticket
honest — the contrast test and the accessibility audit — are installed here, before the content
they guard exists.

The Tokens are declared in two layers, as shadcn does it: raw declarations at the document
root, overridden in a dark scope, and a mapping block that binds them to the framework's own
names. That mapping must be declared as inline, and the reason is not stylistic — without it
the framework freezes each value at compile time and a utility stops following a redefinition
in the dark scope, so the dark theme silently does nothing. With it, switching themes is plain
cascade.

The declared set is exactly the theme's, neither widened nor narrowed. Chart and sidebar Tokens
are declared although nothing renders a chart or a sidebar: a Token vocabulary is fixed upstream
rather than invented here, it costs only stylesheet lines, and splitting it would make the block
undiffable against its source. Tokens the shadcn contract has and this theme does not value are
**not** invented — the day something needs a popover surface is the day its value gets decided
against a real surface. Values keep the hexadecimal notation the design document uses, so the
block stays checkable by eye against its source.

Colour values move where contrast requires, per
[ADR-0004](../../../docs/adr/0004-contrast-outranks-fidelity-to-the-published-theme.md). Three
pairings are known to fail before the work starts and are the reason the rule exists: secondary
text on the page stage, white on the primary fill in both themes, and the input border against
the stage. The primary button is the one that forces the choice rather than illustrating it —
its foreground is already white, which is the maximum attainable against a mid-tone fill, so
nothing but the fill can move. Each moved value carries a note beside it recording the
divergence from the published theme. Any further failure the test surfaces is treated the same
way, never accommodated.

The dark scope declares only what it must. A Token with no dark value inherits its light one and
is derived explicitly **only** where the contrast test fails on the inherited pair — the
mechanism decides, not taste. Two outcomes are known in advance and are a useful check that the
work is right: the focus ring inherits unchanged and clears its threshold on both stages, while
the accent foreground has to be derived, because inherited it lands as a near-black on a
near-black.

Spacing declares nothing: the theme's eight steps are all multiples of four pixels and are
therefore already exactly expressible on the framework's default scale, so naming them again
would create a second vocabulary for values the first one already names. Elevation declares
nothing either: the theme declares no shadows, and the design document's description of them is
the definition of the framework's own small shadow. Radius does declare, because the framework's
scale is offset by one step from the theme's while the design document prescribes control
corners by the framework's *name*. A single root radius plus the shadcn calculation chain
reproduces the theme's five values exactly, which keeps one knob for retuning the whole scale:

```css
--radius: 0.5rem;
--radius-xs: calc(var(--radius) - 4px);  /*  4px */
--radius-sm: calc(var(--radius) - 2px);  /*  6px */
--radius-md: var(--radius);              /*  8px */
--radius-lg: calc(var(--radius) + 2px);  /* 10px */
--radius-xl: calc(var(--radius) + 4px);  /* 12px */
```

The accepted cost is that the medium radius no longer means what the framework's documentation
says it means. That is the price of following the design document's own component instructions,
and it is what shadcn itself does.

The typefaces move to the Fontsource provider, replacing the single remote family the kit ships
with. The providers differ in one way that decides it: the remote ones fetch from a third
party's stylesheet endpoint on every build, while Fontsource resolves from an installed package.
Fonts become versioned dependencies fixed by the lockfile and fetched by the install step the
chain already runs, which is the reproducibility argument of
[ADR-0001](../../../docs/adr/0001-enforced-quality-gate-over-upstream-fidelity.md) applied to
assets, and which removes a network dependency from the browser suite's build step. Only the
weights the design document specifies, latin subset, swap display, no italic. Preloading is
restricted rather than left at the provider default of every variant: the three faces that set
above-the-fold text are preloaded, and the interface medium weight arrives by swap, since it
sets only short labels. The interface face becomes the page default and the reading face becomes
opt-in — the design document forbids setting dense chrome in the reading face, and making it
opt-in is what renders that mistake impossible by default.

Dark mode is selected by a class on the document element, declared to the framework as a custom
variant, because its default binds the dark variant to the media query alone and that leaves no
room for the override ticket 04 will add. A small script in the head applies the class before
the first paint; a deferred module paints first, so this is the only way to avoid a flash of the
wrong theme. The script is written whole here — it reads the stored preference, falls back to
the media query, and sets a class — even though nothing writes a stored preference until ticket
04. It is forbidden from growing beyond those three steps.

The contrast test reads the stylesheet, computes the ratio of every declared pairing in both
themes, and compares it to the thresholds of the standard: 4.5:1 for text, 3:1 for component
boundaries and focus indicators. Which foreground sits on which background is knowledge the
stylesheet does not carry, so the **pairings** are declared in the test — and no colour value
is. Assertions are made against thresholds and never against an expected ratio, which would be
a change detector reddening on every adjustment of hue. A foreground Token appearing in no
pairing fails the test, so a Token added later cannot escape the check by being forgotten.

This test does not belong to the class the quality-gate spec refused — one that reads a
configuration file and restates what it says, so the two can drift. Nothing is restated here:
the colours are declared once in the stylesheet, the thresholds come from the standard, and the
test derives a consequence rather than repeating a value.

The accessibility audit joins the browser test in this ticket rather than at the end of the
effort. Added last, it would find its defects when they are most expensive to fix; added here,
every later ticket has to keep it green while building the content it covers. That is slower on
purpose.

**Parent:** [spec.md](../spec.md)

**Blocked by:** 01

**Status:** ready-for-agent

- [ ] The Tokens are declared in two layers, with the mapping block declared inline
- [ ] Exactly the Tokens the theme declares are present — chart and sidebar Tokens included, and no Token the theme does not value invented
- [ ] Values are written in the notation the design document uses
- [ ] Every declared pairing meets its threshold in both themes: 4.5:1 for text, 3:1 for component boundaries and focus indicators
- [ ] Each value moved away from the published theme carries a note beside it recording the divergence
- [ ] The dark scope declares a value only where the inherited one fails the contrast test; the focus ring is inherited, the accent foreground is derived
- [ ] No spacing Token and no elevation Token is declared
- [ ] The radius scale is declared from a single root radius through the calculation chain, reproducing the theme's five values
- [ ] The three typefaces are delivered by the Fontsource provider, at the weights the design document specifies, latin subset, swap display, no italic
- [ ] The remote font definition the kit shipped with is removed, and the interface font declaration in the stylesheet no longer names it
- [ ] Preloading is limited to the three faces that set above-the-fold text
- [ ] The interface face is the page default; the reading face is opt-in
- [ ] The dark variant is bound to a class on the document element, not to the media query alone
- [ ] A head script applies the theme class before the first paint, reading the stored preference then the media query, and does nothing else
- [ ] A test in the blocking suite reads the stylesheet, computes the ratio of every declared pairing in both themes, and asserts against thresholds — never against an expected ratio
- [ ] The pairings are declared in the test and no colour value is
- [ ] A foreground Token appearing in no pairing fails that test
- [ ] The browser test asserts no accessibility issues at serious impact and above
- [ ] The browser test still asserts console hygiene and the absence of JavaScript errors
- [ ] `composer test` passes
- [ ] `composer browser:test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
