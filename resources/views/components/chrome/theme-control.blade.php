{{-- Chrome rather than a Primitive, and for a reason the purity rule does not itself supply. What
that rule forbids is reaching into the *application*, and a preference held in the browser is not
that — so this component could sit in `components/ui/` and pass `tests/Feature/PrimitivePurityTest.php`
unchanged. It sits here because it is built for this kit's own head script and this kit's own
storage key, which is what makes it unportable, and because shadcn files its own equivalent
outside the primitives too.

Three states, and the third is a state rather than the absence of one. A two-state control
destroys the link to the operating system on the first click with no way back, which is a silent
and irreversible loss for a reader who wanted their system honoured. That symmetry is carried
through to storage: `system` is written to the key like the other two rather than represented by
an empty key. The head script in the layout reads any value that is neither `light` nor `dark` as
a fallback to the media query, so both spellings would work — the explicit one is chosen so that
what is stored and what the control shows are the same three words.

While the system state is active a media-query listener keeps the page following the operating
system as it changes, rather than sampling it once at load. The listener is registered
unconditionally instead of being added and removed with the state: `apply` recomputes from
`theme` every time, so a change event that arrives while light or dark is pinned already resolves
to the same class it just had. Registering once is the version with no lifecycle to get wrong.

The control announces itself through the assistive technology's own vocabulary rather than
through a live region. Three toggle buttons in a labelled group announce as "Colour theme group,
Light toggle button, not pressed", and a change of `aria-pressed` on the focused button is
announced when it happens. The alternative — `role="radio"` — is the more exact match for a
mutually exclusive choice, and it was refused: a radio group owes the reader arrow-key navigation
and a roving tabindex, which is hand-written keyboard JavaScript this page would then have to
observe. Toggle buttons reach the same reader with the browser's own tab order.

The behaviour is Alpine, as the installation command's is, which arrives with Livewire and needs
no dependency added. The pressed styling is the button Primitive's ghost variant keying off the
same `aria-pressed`, so the affordance and the announcement cannot disagree. --}}

<div
    x-data="{
        theme: localStorage.getItem('theme') ?? 'system',
        query: window.matchMedia('(prefers-color-scheme: dark)'),
        init() {
            this.query.addEventListener('change', () => this.apply());
        },
        select(theme) {
            this.theme = theme;
            localStorage.setItem('theme', theme);
            this.apply();
        },
        apply() {
            document.documentElement.classList.toggle('dark', this.theme === 'dark' || (this.theme === 'system' && this.query.matches));
        },
    }"
    role="group"
    aria-label="Colour theme"
    class="flex items-center"
>
    <x-ui.button variant="ghost" x-on:click="select('light')" x-bind:aria-pressed="theme === 'light'">Light</x-ui.button>

    <x-ui.button variant="ghost" x-on:click="select('dark')" x-bind:aria-pressed="theme === 'dark'">Dark</x-ui.button>

    <x-ui.button variant="ghost" x-on:click="select('system')" x-bind:aria-pressed="theme === 'system'">System</x-ui.button>
</div>
