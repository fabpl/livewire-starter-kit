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

**Status:** ready-for-agent

- [ ] A bar and a footer exist as Chrome, composed by the Page rather than placed in the layout
- [ ] The bar uses the page or card surface with foreground links, and carries no primary fill
- [ ] The footer carries the licence and a repository link
- [ ] The theme control offers three states — light, dark, and system — with system a state of its own
- [ ] The choice persists in local storage and is honoured by the head script on the next load
- [ ] While the system state is active, a media-query listener keeps the page following the operating system as it changes
- [ ] The control announces what it does and which state is active
- [ ] The button Primitive gains its ghost variant, and no other variant or size is added
- [ ] The browser suite observes all three states applying, and the choice surviving a reload
- [ ] The browser suite observes the system state following a change of the operating system preference
- [ ] The purity test, the contrast test and the accessibility audit still pass
- [ ] `composer test` passes
- [ ] `composer browser:test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
