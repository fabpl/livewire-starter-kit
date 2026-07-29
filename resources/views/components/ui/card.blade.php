{{-- The second Primitive, and the last this effort builds. Like the button it is a pure function
of its slot, held to that by `tests/Feature/PrimitivePurityTest.php`; unlike the button it takes no
props at all, because it exposes no variants. A card that needed a variant would be a card doing a
second job, and the rule ticket 03 established applies here as it did there — the matrix grows when
something renders the new member, and nothing does.

It carries no structural slots either. shadcn's own card ships a header, a title, a description, a
content region and a footer, and every one of them would be a component whose only consumer is a
test until a second composition wants it. What this one owns is the surface: the fill, the border,
the corner, the padding and the text colour. What sits inside is the composer's.

`--card` and `--background` hold the same value in both scopes of this theme, which is worth
stating because the design document describes a near-white surface layered on the parchment stage
and the layering is not what separates them here — the border is, with the shadow a whisper behind
it. The temptation this invites is a heavier elevation to put the difference back, and it is the
one thing to resist: the theme declares no shadows at all, and `shadow-sm` is already the design
document's own description of them rather than a floor to build on. The Tokens are still the right
ones to name. They are the semantic pair for a card surface, a theme swapped into this contract may
well separate them, and `--card-foreground` is genuinely not `--foreground` — it is the darker of
the two in light and the lighter in dark, so the text inside a card already reads differently from
the text beside it. `tests/Feature/ContrastTest.php` holds that pair at 4.5:1 in both themes.

`rounded-lg` is 10px through the calculation chain in the stylesheet rather than Tailwind's
documented 8px, which is the accepted cost recorded there: the design document prescribes corners
by Tailwind's name and the scale is offset by one step from the theme's.

The consumer convention is the button's, and it is the same convention for the same reason:
Laravel's attribute merge concatenates, so the class attribute of a Primitive carries positioning
and spacing utilities only. What must vary is a variant, and this Primitive has none to add to.

The block below holds a constant that is used once, which the button needs for its `match` and
this does not — so it is worth saying why it is here rather than inlined into the call. Passed
inline, Prettier breaks the argument across four lines and leaves the class list indented inside
a wrapped method call, which is harder to read than the string on its own and diffs badly on
every future change to it. Naming it is what keeps the markup one line, and it is the shape the
next Primitive will copy. --}}

@php
    $classes = 'rounded-lg border border-border bg-card p-6 text-card-foreground shadow-sm';
@endphp

<div {{ $attributes->class($classes) }}>{{ $slot }}</div>
