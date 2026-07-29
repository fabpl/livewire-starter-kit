# Contrast outranks fidelity to the published theme

The interface adopts the "Claude" shadcn theme published by tweakcn — terracotta on
parchment, Poppins over Lato over Lora. Three of the pairings that theme declares fail
WCAG 2.2 level AA: `muted-foreground` on `background` at 3.57:1, white on `primary` at
3.86:1 in light and 3.14:1 in dark, and the input border at 2.03:1 where a component
boundary requires 3:1. Where a pairing fails, the token value moves until it passes, and
the divergence from the published theme is recorded beside the value.

The primary button is the case that forces the choice rather than merely illustrating it.
`primary-foreground` is already `#ffffff`, which is the maximum attainable against a
mid-tone fill; no adjustment of the text rescues that pairing, and the only remaining lever
is darkening the terracotta by roughly seventeen per cent of luminance. Fidelity to the
published values and level AA cannot both hold, so one of them has to be the constraint and
the other the aspiration.

The bar is enforced rather than intended. A test in the blocking suite reads
`resources/css/app.css`, computes the ratio of every declared pairing in both themes, and
compares it to the thresholds of the standard — 4.5:1 for text, 3:1 for component
boundaries and focus indicators. This does not restate configuration in a second place:
the colours are declared once, in the stylesheet; the thresholds come from WCAG; the test
holds neither, it derives a consequence. Assertions are made against the thresholds and
never against an expected ratio, which would be a change detector on every adjustment of
hue. A `*-foreground` token appearing in no pairing fails the test, so a token added later
cannot escape the check by being forgotten.

## Considered options

**The theme first, no bar at all.** The failing values are not decorative:
`muted-foreground` carries every piece of secondary text in the kit, and the other is the
label of the primary button. They are the two most repeated surfaces of a design system,
and shipping them wrong propagates them into every project that starts from this
foundation.

**AA as a target, with a documented exception list.** An exception list is a suppression
mechanism, and this repository refuses suppression mechanisms in every other tool it
configures.

**An axe audit in the browser suite alone.** That suite is deliberately not part of the
blocking gate, so the bar would be prose. It is kept in addition rather than instead, for
what arithmetic cannot see — roles, labels, heading order.

## Consequences

The kit's terracotta is not `#cb6441`. Screenshots and token dumps will not match tweakcn's
output, and a contributor comparing the two will find a difference that is deliberate.

Only colour is held mechanically. Focus visibility, target size, motion preferences and
semantic structure are not arithmetic and are not held by this test; the browser suite's
axe audit reaches some of them, and it does not block.
