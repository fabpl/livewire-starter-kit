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

        {{-- The guarantees, and the block that makes this page true: it is where the repository
        stops describing a framework and starts describing itself. Every claim below is checkable
        by opening a file in this tree — `phpstan.neon` for the level and the two rule sets,
        `composer.json` for the two minimums, `bin/check-annotations.php` for the last — and a
        claim that stops being true when a configuration changes is a claim that should fail
        review.

        Three cards and no more, because three is what the gate actually guarantees in categories
        a reader can hold apart: what the analyser reads, what the tests are held to, and what
        nothing is exempt from. A fourth would be one of these three restated.

        The bodies are set in the reading face, which makes this the second and last place on the
        page that opts into it. The headings stay in the display face and the interface face is
        still the document default, which is what keeps the design document's prohibition on dense
        chrome in the reading face impossible to break by accident.

        The section is labelled by its own heading rather than carrying an `aria-label`, so the
        landmark announces the words on the screen instead of a second phrasing only a screen
        reader hears. Heading order is h1, h2, h3 with nothing skipped — the accessibility audit
        in the browser suite reads it. --}}
        <section class="mt-24 sm:mt-32" aria-labelledby="guarantees-heading">
            <h2 id="guarantees-heading" class="font-display text-2xl font-semibold tracking-tight text-balance sm:text-3xl">
                What the gate guarantees
            </h2>

            {{-- Medium-low density: a six-unit gutter between the cards and the same six inside
            them, from the Primitive. The measure follows from the grid rather than from a width
            set on the prose, and what three columns inside this shell leave is a column of a
            little under forty characters. That is under the range long-form body text wants,
            which is why each body is two sentences rather than a passage — a card is read in a
            glance, and a paragraph that needed a wider measure would be a paragraph that did not
            belong in a card.

            Two columns before three, and the middle step is not decoration: at the tablet width
            a third column would take that measure below thirty. The odd card sits alone on the
            second row there, which is the honest cost of three items in a two-column grid and
            cheaper than an unreadable line. --}}
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <x-ui.card>
                    <h3 class="font-display text-base font-semibold text-balance">Static analysis at level 10</h3>

                    <p class="mt-3 font-serif text-sm text-pretty">PHPStan reads the application, its configuration, its routes and its own tests at level 10. Two rule sets sit beyond the levels altogether, so raising the level alone would never bring them in: the strict set rejects loose constructs the language still permits, and the deprecation set fails on framework API already scheduled for removal.</p>
                </x-ui.card>

                <x-ui.card>
                    <h3 class="font-display text-base font-semibold text-balance">Coverage at a hundred per cent</h3>

                    <p class="mt-3 font-serif text-sm text-pretty">The blocking command fails below full line coverage. A covered line is one that ran rather than one that was tested, so a second command mutates the code and reports how much of it the suite notices — 75 per cent today, and it fails under that.</p>
                </x-ui.card>

                <x-ui.card>
                    <h3 class="font-display text-base font-semibold text-balance">No suppression mechanism</h3>

                    <p class="mt-3 font-serif text-sm text-pretty">Pint and Prettier format the whole tree and Rector refactors it, and the command fails when any of the three would change a file. Nothing is excused from the rest: no analyser baseline, no ignored errors, and a check in that same command rejects the annotations that would exempt a line from coverage or from mutation.</p>
                </x-ui.card>
            </div>
        </section>
    </main>

    <x-chrome.footer />
</div>
