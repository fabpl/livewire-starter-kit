# 04 — The bar, the footer and the theme control

**What to build:** the Page gains its Chrome — a quiet bar at the top, a footer at the bottom,
and the control that makes the dark theme reachable by a reader rather than only by their
operating system.

Bar and footer are Chrome, not Primitives: they are specific to this kit and not portable, and
they are composed by the Page rather than placed in the layout. The layout carries the document
only, which is what lets the first authenticated screen of a real project bring its own shell
without unpicking this one.

The bar is treated as chrome in the visual sense too. The design document is explicit that a
navigation bar is not filled with the brand colour: the bar takes the page or card surface, its
links take the foreground, and the terracotta is spent in the content pane. There is one primary
call to action on this page and it is in the hero, not here.

The theme control has three states — light, dark, and system — and system is a state of its own
rather than the absence of one. A two-state control destroys the link to the operating system on
the first click with no way back, which is a silent and irreversible loss for the reader. The
choice persists in local storage, which is what the head script installed in ticket 02 already
reads. While the system state is active, a media-query listener keeps the page following the
operating system as it changes, rather than sampling it once at load.

The control is Chrome and it holds client state, which the purity rule permits — that rule
forbids reaching into the *application*, and a preference held in the browser is not that. It is
still not a Primitive: it is built for this kit, and shadcn classes its own equivalent outside
the primitives too.

The button Primitive gains its ghost variant here, because this is the first place that renders
one — the bar's link and the theme control. The variant matrix grows only when something renders
the new member, which is the rule ticket 03 established.

Accessibility is not an afterthought on this control: it announces what it does and which state
is active, so it is operable without sight. The audit installed in ticket 02 covers roles and
labels, and it has to stay green with the control present.

The theme switch is the one piece of hand-written JavaScript in this tree, so the browser suite
observes it end to end: each of the three states applies, the choice survives a reload, and the
system state follows the operating system rather than a value captured at load.

**Parent:** [spec.md](../spec.md)

**Blocked by:** 03

**Status:** resolved

- [x] A bar and a footer exist as Chrome, composed by the Page rather than placed in the layout
- [x] The bar uses the page or card surface with foreground links, and carries no primary fill
- [x] The footer carries the licence and a repository link — and the theme control, see the first comment
- [x] The theme control offers three states — light, dark, and system — with system a state of its own
- [x] The choice persists in local storage and is honoured by the head script on the next load
- [x] While the system state is active, a media-query listener keeps the page following the operating system as it changes
- [x] The control announces what it does and which state is active
- [x] The button Primitive gains its ghost variant, and no other variant or size is added
- [x] The browser suite observes all three states applying, and the choice surviving a reload
- [x] The browser suite observes the system state following a change of the operating system preference — with the event dispatched by the test, see the second comment
- [x] The purity test, the contrast test and the accessibility audit still pass
- [x] `composer test` passes
- [x] `composer browser:test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

**The theme control moved to the footer, on the maintainer's call.** `spec.md` put it in the bar
— *"a quiet bar carrying the kit's name, the theme control and a repository link"* — and it was
built there. Seen on the page, three segments carrying the widest labels in the composition were
the loudest thing on a bar whose whole brief is to be quiet, and a preference a reader sets once
does not need to be the second thing they meet. The bar keeps the kit's name and the repository
link; `spec.md`'s composition section was corrected to match rather than left to disagree with the
page. What it costs is a reader in dark mode scrolling to find the switch, which on a page this
short is one gesture. Nothing about the control itself changed, and no test needed rewriting:
they find it by its labels and by its accessible name, neither of which is a position.

**Adjacent ghost buttons drew over each other, and the fix is a gap the composer owes them.** The
segments were flush, and hovering one drew its outline across its neighbour's pressed ring. The
Primitive's two affordances both reach outside their own box — the ring extends two pixels past
the border box, the hover outline sits two pixels clear of it and is two pixels thick — so six
pixels is where they stop meeting and eight is the next step up. This is a property of the
Primitive that its consumers have to know, so it is written in the theme control beside the gap
rather than left to be rediscovered.

**The ghost variant carries the pressed state, which is a decision about where variance lives.**
The obvious alternative was for the theme control to pass the active segment's colours in the
button's class attribute, and ticket 03's convention forbids exactly that: Laravel's attribute
merge concatenates, so a colour passed by a caller does not reliably beat the component's own.
The pressed state is therefore keyed off `aria-pressed` inside the variant table, which means the
affordance and the announcement come from one attribute and cannot disagree. A ghost button that
is never pressed — the bar's repository link — simply never matches. The pairing it lands on,
accent foreground on accent, was already held at 4.5:1 in both themes by `ContrastTest`.

**The pressed state was drawn in a fill no mechanism was holding, and review caught it.** The
first implementation identified the active theme state with `--accent` alone. That is 1.19:1
against the page stage in light and 1.16:1 in dark, and WCAG 1.4.11 reaches the visual
information required to identify a component **and its state** — so the one thing this control
exists to communicate was below the threshold the rest of the kit is held to, on a page whose
argument is that colour contrast is arithmetic in the blocking suite. Neither guard could see it:
`ContrastTest` pairs foregrounds with backgrounds and `--accent`-as-a-surface is not a foreground,
while axe's colour rule compares text against its background and not two backgrounds against each
other. The state now carries a two-pixel ring in `--input`, the Token for a control's boundary,
which that test already holds at 3.04:1 and 3.05:1 against the stage. `--accent` on `--background`
is deliberately not added to the pairings, for the reason already recorded there for `--border`:
once the ring identifies the state the fill is a courtesy, and holding it would darken every
accent surface in the kit to satisfy a rule the standard does not impose. Worth keeping in view
for a later effort: this is the second boundary case the pairing table cannot express, and a third
is the moment to ask whether it wants a notion of a surface-on-surface pairing.

**The harness cannot change the operating system's preference after a page loads.** Playwright
emulates a colour scheme when a browser context is created, and `pest-plugin-browser` exposes
that as `inDarkMode`; neither exposes `emulateMedia`, so there is no way to flip the preference
mid-test. The listener is therefore observed by dispatching a `change` event on the component's
own media query list, reached through Alpine's public `$data` accessor and the control's
accessible name, with the class stripped first so that a listener which was never registered
cannot pass. Everything but the arrival of the event is real, including `matches`, which reports
the genuinely emulated preference. Both that test and the persistence test were checked by
falsification — removing the listener reddens one, removing the write to storage reddens the
other, and removing the `aria-pressed` binding reddens the assertion on the announced state.

**A radio group was refused for the theme control.** `role="radio"` is the more exact match for a
mutually exclusive choice, and it owes the reader arrow-key navigation and a roving tabindex —
hand-written keyboard JavaScript that the browser suite would then also have to observe. Three
toggle buttons in a labelled group announce the same two facts, what the control does and which
state is active, using the browser's own tab order. The spec's "one piece of hand-written
JavaScript" stays one piece.

**System is written to storage rather than represented by an empty key.** The head script reads
any value that is neither `light` nor `dark` as a fallback to the media query, so clearing the key
would have worked too. Storing the word keeps the three states symmetric in the one place the
ticket insists they are symmetric, and makes what is stored the same three words the control
shows.

**Rector failed on a cache belonging to a deleted worktree.** Every file in the tree reported a
phar path under `.claude/worktrees/hero-guard-primitives-6b2048`, which no longer exists. Rector's
own cache is per-checkout, but the PHPStan it embeds caches its reflection stubs under the shared
system temporary directory, and nothing there distinguishes one worktree from another. Clearing
that directory fixed it. Recorded here because the failure names every file in the repository and
reads like a catastrophic defect in the change under test.
