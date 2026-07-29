@props(['variant' => 'primary', 'href' => null])

{{-- The first Primitive: a pure function of its props and its slot, held to that by
`tests/Feature/PrimitivePurityTest.php`. Nothing here names an application symbol, and nothing
here reads ambient state.

Three variants at one size, because three variants at one size is what the composition renders.
The outline variant and the small and large sizes are absent on purpose: the seam that would
verify them is the Page that renders them, so building them would mean either shipping
unverified code or inventing a demonstration view whose only consumer is a test.

The ghost variant carries no fill, which is what the bar wants: the design document is explicit
that a navigation bar is not filled with the brand colour, and the one primary fill on this page
is spent in the hero. It also carries its own pressed state, and that is the reason the state is
here rather than in the caller's class attribute. A consumer that wanted to colour the active
segment of the theme control itself would be passing a colour utility, which the convention below
forbids precisely because it does not reliably win. `aria-pressed` is already how the control
announces which state is active, so the variant styles what the assistive technology is told —
one source for the affordance rather than two that can disagree. A ghost button that is never
pressed, such as the bar's repository link, simply never matches.

What identifies the pressed state is the ring, not the fill, and that correction came out of
review. WCAG 1.4.11 reaches the visual information required to identify a component *and its
state*, and `--accent` on the page stage is 1.19:1 in light and 1.16:1 in dark — so a fill alone
left the one thing this control exists to communicate below a threshold the rest of the kit is
held to. The text moves with it, from the foreground to the accent foreground, but in light mode
those are two near-identical browns and it carries nothing. `--input` does carry it: it is the
Token for the boundary of a control, `tests/Feature/ContrastTest.php` already holds it at 3:1
against the stage in both themes, and a ring is a box shadow rather than a border, so it costs no
layout. Focus keeps the outline in `--ring`, which leaves selection and focus drawn in two
different Tokens instead of one doing both jobs badly.

`--accent` on `--background` is deliberately *not* added to that test's pairings, and the reason
is the one already written there for `--border`: 1.4.11 does not ask a decorative fill to meet a
threshold, only the information required to identify the component. Once the ring identifies the
state, the fill is a courtesy to sighted readers and holding it would darken every accent surface
in the kit to satisfy a rule the standard does not impose. The fill's own text pairing, accent
foreground on accent, is held at 4.5:1 in both themes and always was.

The variant table is inline, which is the exemption ADR-0003 grants. `match` rather than an array
lookup so that an unknown variant raises where it is passed instead of falling back to a default
and rendering the wrong control in silence.

Consumers may pass a class, and the convention — not a dependency — bounds what it may carry:
positioning and spacing utilities only. Laravel's merge concatenates, so a colour passed by a
caller does not reliably beat the one below; the winner would be stylesheet order rather than
intent. What must vary is a variant, and a missing variant is added rather than worked around.

The label weight is 700 rather than 500. The design document asks for the interface face at
medium for short labels, and `vite.config.js` records why that is not expressible: Lato ships no
500, so `font-medium` would resolve to 400 by CSS font matching and render as body text. 700 is
the weight the build actually loads.

Hover and focus are drawn as a ring in the focus Token rather than as a change to the fill, and
that is a contrast decision rather than a stylistic one. `--primary` was already moved once, per
ADR-0004, to bring its white label to 4.58:1 — barely over the threshold, with white already at
its maximum. Every way Tailwind offers to soften a fill on hover composites it against the page,
which on the parchment stage lightens it and drops that same label under 4.5:1; a filter dims the
label alongside it and lands at 4.46:1. So the fill does not move. `--ring` is held at 3:1
against the stage by `tests/Feature/ContrastTest.php`, which makes both affordances properties
the suite already verifies. --}}

@php
    $classes =
        'inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-5 py-2.5 text-sm font-bold outline-ring outline-offset-2 hover:outline-2 focus-visible:outline-2 ' .
        match ($variant) {
            'primary' => 'bg-primary text-primary-foreground',
            'secondary' => 'bg-secondary text-secondary-foreground',
            'ghost' => 'text-foreground aria-pressed:bg-accent aria-pressed:text-accent-foreground aria-pressed:ring-2 aria-pressed:ring-input',
        };
@endphp

@if ($href === null)
    <button {{ $attributes->class($classes)->merge(['type' => 'button']) }}>{{ $slot }}</button>
@else
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@endif
