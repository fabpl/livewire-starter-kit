@props(['variant' => 'primary', 'href' => null])

{{-- The first Primitive: a pure function of its props and its slot, held to that by
`tests/Feature/PrimitivePurityTest.php`. Nothing here names an application symbol, and nothing
here reads ambient state.

Two variants at one size, because two variants at one size is what the composition renders. The
outline variant and the small and large sizes are absent on purpose: the seam that would verify
them is the Page that renders them, so building them would mean either shipping unverified code
or inventing a demonstration view whose only consumer is a test. The ghost variant arrives with
the bar that renders it.

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
        };
@endphp

@if ($href === null)
    <button {{ $attributes->class($classes)->merge(['type' => 'button']) }}>{{ $slot }}</button>
@else
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@endif
