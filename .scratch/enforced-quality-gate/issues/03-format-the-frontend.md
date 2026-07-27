# 03 — Format the frontend with Prettier

**What to build:** everything that is not plain PHP and not prose gets formatted mechanically —
templates, styles, scripts and workflow files — with utility classes sorted inside the
templates. A developer stops deciding how a template is indented or in what order its classes
appear, and reviewers stop mentioning it.

Documentation was in this ticket's scope when it was written and is no longer: markdown is
excluded by a maintainer decision taken during implementation, recorded in the Comments below.

The plugin combination is already settled by measurement, and the losing option is worth
naming so it is not proposed again: the better-known Blade plugin formats templates but leaves
Tailwind classes **unsorted**, because the Tailwind plugin monopolises a Prettier API and
carries workarounds for a fixed list of plugins that includes no Blade plugin. The chosen
Blade plugin declares the Tailwind plugin as a peer dependency, and sorting then works. A
third plugin, for PHP, is not an addition — npm demands it as a peer dependency of the first.

That third plugin is also the risk in this ticket. It registers itself on the PHP extension
and was measured claiming an ordinary source file in a probe, which would put two fixers on
PHP and break the ownership rule. Prettier's ignore file is what materialises the boundary
with Pint, so the criterion below is that Prettier's reach is *verified*, not assumed.

This ticket blocks nothing and nothing blocks it — it can run in parallel with the whole PHP
chain.

**Blocked by:** None — can start immediately.

**Status:** resolved

- [x] Prettier and its three plugins are development dependencies, with the Tailwind plugin loaded last
- [x] The Tailwind plugin is pointed at the project's stylesheet so it sorts against the real theme
- [x] Quote style is set explicitly, so template output agrees with Pint instead of switching to double quotes
- [x] Indentation agrees with the editor configuration, verified file by file rather than assumed
- [x] Prettier is verified to claim no plain PHP file, and to still claim templates
- [x] Dependency manifests and lockfiles are excluded, so installing a dependency cannot turn the pipeline red
- [x] A fix command and a check command both exist
- [x] The aggregate command runs the check command
- [x] The pipeline installs the frontend dependencies before running the gate
- [x] Utility classes are demonstrably sorted in a template
- [x] Every directive in the existing template survives the reformat unchanged
- [x] The whole eligible tree is formatted and committed
- [x] Running Prettier a second time produces no further change
- [x] `composer test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

**Markdown excluded from Prettier's reach.** The ticket and the spec both put documentation in
scope, on the measurement that the treatment was harmless. During implementation the maintainer
reversed that: prose files are read by humans, and realigned tables plus rewritten emphasis
markers (`*maximum*` → `_maximum_`) are churn on a surface where no reviewer was arguing about
formatting in the first place. Eight markdown files had been reformatted and were restored. The
reasoning lives in `.prettierignore`, next to the rule.

This is the escalation route the spec describes working as designed — a human changing the
configuration deliberately, not an agent routing around a check. The corresponding paragraph in
the spec's *Frontend formatting — Prettier* section was updated in the same effort.

**Measurements taken during implementation**, since the ticket asked for verification rather
than assumption:

| Property | Result |
| --- | --- |
| Prettier's reach before the ignore file | 28 plain PHP files claimed, including `artisan` via its shebang |
| Prettier's reach after the ignore file | the Blade template alone |
| Files Prettier now owns | 7 — two workflow YAML, `pint.json`, one CSS, two JS, one Blade template |
| Utility-class sorting | `bg-[#FDFDFC] dark:bg-… text-… flex p-6` → `flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6` |
| Directive survival | inventory byte-identical to the previous commit, including `@fonts`, `@vite(` and `{{-- --}}` |
| Indentation | 4-space multiples everywhere, YAML 2, zero tabs |
| Idempotence | second and third passes produce no change |

**Two settings the ticket did not anticipate.** `bladeDirectiveArgSpacing` defaults to `space`,
which rewrote `@vite(...)` to `@vite (...)`; set to `none`, the plugin's own override list keeps
`@if (...)` spaced, which is exactly Laravel's convention. And `printWidth` had to be widened
from the default 80 — at 80 the plugin broke `<title>{{ __('Welcome') }}</title>` across seven
lines and inserted a trailing comma inside a Blade echo.

**`composer fix` is documented but unowned.** The quality-gate skill describes it as running
Rector, then Pint, then Prettier. No ticket in this effort creates it. This ticket added
`composer format` and `composer format:check`, mirroring the existing `lint` / `lint:check`
pair; the aggregate fixer needs Rector and so belongs with ticket 05.
