{{-- The footer, composed by the Page for the same reason the bar is. Two facts — the licence the
kit is released under and where to read it — and the theme control. Both facts are checkable
against this repository: `composer.json` declares the licence, and the address is the one the
installation command in the hero names.

The theme control is here rather than in the bar, which is a divergence from `spec.md` taken on
the maintainer's call and recorded in ticket 04. The argument for it is that a preference set once
does not belong among the first things a reader meets, and that three segments carrying the widest
labels in the composition made the bar the loudest quiet thing on the page. What it costs is a
reader in dark mode having to scroll to find the switch, on a page short enough that the footer is
one gesture away.

The repository link is an ordinary anchor rather than the button Primitive, and that is not an
oversight. The bar's link is a control on its own in a bar and reads as one; a link in a line of
running text reads as text, and dressing it as a button would claim a prominence the footer is
deliberately not asking for. It is underlined rather than distinguished by colour alone, so that
it is still identifiable to a reader who does not perceive the hue difference.

Only the focus ring is borrowed from the Primitive, and only because a keyboard user crossing
from the bar into the footer should not meet a second idea of what focus looks like. Hover is the
anchor's own — the text moves to the foreground, which is an affordance running text already
has — so the ring is not repeated there.

The text is set at the muted foreground, which `tests/Feature/ContrastTest.php` already holds at
4.5:1 against the stage in both themes — the pairing whose published value that test rejected
first. --}}

<footer class="border-t border-border">
    <div class="mx-auto flex w-full max-w-5xl flex-wrap items-center justify-between gap-x-6 gap-y-4 px-6 py-6">
        <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground">
            <p>Released under the MIT licence.</p>

            <a
                href="https://github.com/fabpl/livewire-starter-kit"
                class="rounded-md underline underline-offset-4 outline-offset-2 outline-ring hover:text-foreground focus-visible:outline-2"
            >
                github.com/fabpl/livewire-starter-kit
            </a>
        </div>

        <x-chrome.theme-control />
    </div>
</footer>
