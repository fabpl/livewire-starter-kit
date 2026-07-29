{{-- Chrome rather than a Primitive, and the distinction is not filing. This is built for one
piece of content — the command that installs *this* kit — so it is not portable to another
project, which is precisely what separates the two categories in `CONTEXT.md`. It therefore sits
outside `components/ui/` and outside the reach of `tests/Feature/PrimitivePurityTest.php`.

The behaviour is Alpine, which arrives with Livewire and needs no dependency added. The command
is read back out of the rendered `<code>` rather than repeated in the handler, so the text that
is copied is the text that is on screen and the two cannot drift.

Success is reported in a live region rather than by swapping the button's label, so that a reader
who cannot see the change is told about it too — `role="status"` carries an implicit polite live
region, which announces the insertion. The region is rendered empty and always present, because a
live region inserted at the moment it has something to say is a region the assistive technology
was not watching. It clears after two seconds so that a second copy announces as well as the
first; that is the reason for the timer, not decoration.

The command is set in the monospace stack, and that is not a fourth typeface arriving by the back
door. `font-mono` resolves to the platform's own fixed-width family: nothing is downloaded, so
none of the reproducibility or preloading arguments behind the three self-hosted faces apply, and
no `--font-mono` Token is declared or wanted. What it buys is that a shell command reads as a
shell command — aligned, unambiguous about the hyphens and the slash — which a proportional face
takes away.

The flag is set inside `then` rather than beside the call, so the confirmation follows the write
succeeding instead of the click happening. `writeText` returns a promise and there is no `catch`:
one that set the flag anyway would report a success that did not happen, and one that did nothing
would be indistinguishable from the rejection travelling unhandled. So a failed write is silent
and shows nothing, which is at least truthful — the reader is not told a copy happened.

Do not read that silence as caught elsewhere. It was measured: an unhandled rejection here
reaches neither `assertNoConsoleLogs` nor `assertNoJavaScriptErrors` in the browser suite. The
only thing that would notice is the assertion on the confirmation itself, which is why
`tests/Browser/HomePageTest.php` makes one. --}}

<div
    x-data="{
        copied: false,
        copy() {
            navigator.clipboard.writeText(this.$refs.command.textContent.trim()).then(() => {
                this.copied = true;
                setTimeout(() => (this.copied = false), 2000);
            });
        },
    }"
    class="flex max-w-full flex-wrap items-center gap-3"
>
    <div class="flex max-w-full items-center gap-3 rounded-md border border-input bg-card py-1.5 pr-1.5 pl-4">
        <code x-ref="command" class="min-w-0 overflow-x-auto font-mono text-xs whitespace-nowrap text-card-foreground sm:text-sm">
            laravel new my-app --using=fabpl/livewire-starter-kit
        </code>

        <x-ui.button variant="primary" x-on:click="copy()">Copy</x-ui.button>
    </div>

    <span role="status" class="text-sm text-muted-foreground" x-text="copied ? 'Copied' : ''"></span>
</div>
