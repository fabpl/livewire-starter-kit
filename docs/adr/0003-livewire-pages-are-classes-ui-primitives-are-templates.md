# Livewire pages are classes, UI primitives are templates

The repository grows its first interface layer, and two kinds of component appear with it.
They are placed on opposite sides of the analysers' perimeter, and the line between them is
a directory rather than a judgement.

A **page** is a class-based Livewire component under `app/Livewire/Pages/`, whose view is
resolved implicitly from `resources/views/`. Livewire 4's own default — a single-file
component holding an anonymous class in the head of the template — is refused. A
**primitive** is an anonymous Blade template under `resources/views/components/ui/`, and it
may carry its variant table inline, outside every analyser.

The refusal of single-file components is [ADR-0001](0001-enforced-quality-gate-over-upstream-fidelity.md)
applied literally. PHPStan, Rector, Pint, the coverage minimum and the mutation score all
read `app/`; none of them reads `resources/`. A single-file component moves typed
properties, actions and validation rules into the one directory nothing inspects — no
`declare(strict_types=1)` imposed, no level 10, and a method never called that cannot pull
coverage below its minimum. On the page, which is the form every future page will copy, that
is the gate's own thesis inverted.

The exemption granted to primitives is not a softening of the same rule. Coverage and
mutation applied to a table mapping a `variant` prop to a string of classes produce
tautological tests: the implementation restated in a test file, a change detector that
reddens on every visual adjustment and has never caught a defect. Paid on every variant of
every component, that is ceremony rather than quality.

What makes the exemption safe is that it is bounded and mechanically guarded. A primitive is
a pure function of its `@props` and its slots: it names no application symbol and reaches no
ambient state — no `App\`, no facade, no `auth()`, `request()`, `session()`, `config()`,
`route()` or `old()`, with `__()` the single allowance. A test in the blocking suite walks
the directory and fails on any forbidden pattern, naming the file and the pattern, in the
manner `RoutingTest` already forbids route closures by traversal rather than by instruction.
The rule earns something beyond safety: a component that knows only its props is portable
by construction, which is the promise the shadcn idiom makes in prose and this one keeps as
a verified property.

## Considered options

**Single-file components throughout.** Follows Livewire 4 and needs no configuration. Not
mechanically recoverable: PHPStan does not analyse Blade, and measuring coverage would mean
measuring templates compiled into `storage/`.

**Class-based components for the UI layer as well.** Fully analysed, typed props checked at
level 10 — and it forfeits Blaze for the whole layer. `BladeService::componentNameToPath()`
returns an empty string when the component resolves to a class, so `Folder::fold()` leaves
it unfolded. `livewire/blaze` is a `require` dependency here; keeping it installed and inert
is worse than either coherent position.

**Case by case — a class where there is logic, a template where there is not.** Rejected for
the reason ADR-0001 rejected its own two narrower positions: the frontier becomes the gap
agents fall through, and no linter can adjudicate a judgement. A path can be adjudicated; a
degree of "presentational" cannot.

## Consequences

`config/livewire.php` is published, its `view_path` set to `resource_path('views')` so that
a component's view mirrors its class path without a `render()` method, and `pages` is removed
from `component_namespaces` so that `resources/views/pages/` has one resolver rather than
two. Because the mirror starts at the root of `resources/views/`, no Livewire component may
sit at the root of `App\Livewire` — it would write its view among the ordinary views. An
architecture test holds that.

Blaze folds primitives, and folds them well: when a variant is passed as a literal, the table
resolves at compile time and the abstraction costs nothing at runtime.

The honest cost is that the gate no longer covers the tree uniformly. "All PHP in this
repository is analysed" becomes "all PHP is analysed except under one named directory, whose
contents are held by a different test" — a second rule to know, and a second thing that can
be forgotten.
