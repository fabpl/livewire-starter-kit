{{-- The bar, composed by the Page rather than placed in the layout. That is the whole reason
the layout carries the document only: this composition wants a marketing bar and the first
authenticated screen of a real project will want a different shell, so fusing either into the
global layout would force the first real consumer to unpick the kit's before using it.

Chrome in the visual sense as well as the structural one. The design document is explicit that a
navigation bar is not filled with the brand colour: the bar takes the page surface, its links
take the foreground, and the terracotta is spent in the content pane. There is one primary call
to action on this page and it is in the hero. The bar is separated from the stage by a rule
rather than by a second surface, which keeps the parchment continuous behind it.

The kit's name is set as text and not as a link home. The repository serves one route, so a brand
link would lead to the page the reader is already on — the same decorative lie the hero's calls to
action exist to avoid. It becomes a link the day there is a second Page.

Nothing here is `sticky`. A bar pinned over a page this short takes vertical space from the
content for no navigation it enables.

The theme control is not here, and `spec.md` originally put it here. It sits in the footer
instead, on the maintainer's call: three segments carrying the widest labels in the bar was the
loudest thing on a bar whose whole brief is to be quiet, and a preference a reader sets once does
not need to be the second thing they meet. What that leaves at the top is the kit's name and the
one link a reader might want before scrolling. --}}

<header class="border-b border-border">
    <div class="mx-auto flex w-full max-w-5xl flex-wrap items-center justify-between gap-x-6 gap-y-2 px-6 py-3">
        <p class="font-display text-base font-semibold text-foreground">livewire-starter-kit</p>

        <x-ui.button variant="ghost" href="https://github.com/fabpl/livewire-starter-kit">GitHub</x-ui.button>
    </div>
</header>
