{{-- The Page composes its own Chrome. The layout carries the document and nothing else, so the
bar and the footer are assembled here — and the column below is what makes the footer sit at the
bottom of a short viewport rather than halfway up it.

The three landmarks are the reason the content is not simply three siblings: a banner, a main
region and a content info, which is the structure the accessibility audit in the browser suite
reads and the structure a reader navigating by landmark expects. --}}

<div class="flex min-h-dvh flex-col">
    <x-chrome.header />

    <main class="mx-auto w-full max-w-5xl flex-1 px-6 py-24 sm:py-32">
        {{-- The hero. Typography follows the design document: the headline in the display face, and
        the supporting line in the reading face — the one place on this page that opts *into* it,
        and the reason the interface face is the document default rather than merely the
        recommended one.

        The measure is narrower than the shell. Chrome may run the full width; prose may not,
        because a line of body text that crosses a wide viewport stops being readable. --}}
        <section class="max-w-3xl">
            <h1 class="font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                A Laravel foundation that cannot be quietly weakened
            </h1>

            {{-- Every clause is checkable against this repository, which is the bar the spec sets
            for copy on this page. `composer test` runs the annotation check, Pint, Prettier,
            Rector, PHPStan and Pest at a hundred per cent; `composer ci:check` is that same
            script, so the pipeline runs the command rather than a variation on it; and the tree
            carries no PHPStan baseline, no ignored errors, no excluded paths and no coverage or
            mutation suppression.

            Mutation testing is deliberately not in this sentence. It exists here as `composer
            mutate`, but it is not part of the blocking command and CI does not run it, so listing
            it among the things "one command holds" would be the drift into marketing the spec
            forbids. Ticket 05 states the score on its own terms. --}}
            <p class="mt-6 font-serif text-lg text-pretty text-muted-foreground sm:text-xl">Static analysis, full coverage, formatting and automated refactoring — one command holds all of it, the pipeline runs that same command, and there is no suppression mechanism anywhere in the tree.</p>

            {{-- Both calls to action do something, which is a correctness requirement rather than
            a flourish: this repository serves one route, so two buttons leading nowhere on the
            page that argues for rigour would be a decorative lie. --}}
            <div class="mt-10 flex flex-wrap items-center gap-4">
                <x-chrome.install-command />

                <x-ui.button variant="secondary" href="https://github.com/fabpl/livewire-starter-kit">Read the source</x-ui.button>
            </div>
        </section>
    </main>

    <x-chrome.footer />
</div>
