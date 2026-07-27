# Spec: enforced quality gate

Status: ready-for-agent

## Problem Statement

The repository's quality gate is the one the starter kit ships: Pint on its default
Laravel preset, Larastan at level 7, PHPUnit with no coverage requirement. Nothing in
that configuration is wrong, and nothing in it constrains anything. Pint's default
preset reports zero findings on the tree. Larastan reports zero findings — and would
report zero at its maximum level too, measured. The gate passes because it asks almost
nothing, not because the code is good.

This matters because of who writes the code here. The repository is tooled for agents:
a commit convention, an issue tracker convention, domain documentation rules, a skills
directory. Agents drift stylistically, comment redundantly, leave code untested, and
reach for the nearest escape hatch when a check fails. Prose instructions in `CLAUDE.md`
do not stop any of that; only a mechanical gate does.

The window to act is now and it is closing. The repository holds 30 PHP files, of which
28 arrived with the kit, plus three frontend files and two example tests. Every
reformatting decision taken today costs a diff nobody will read. The same decision taken
after the first real feature costs a migration.

## Solution

Install and wire a quality chain that enforces the strictest position each tool can
express, applied without exception to the entire tree, and gate every commit on it.

Five tools, each owning a distinct concern: Rector rewrites structure, Pint formats
syntax, Larastan analyses types, Pest verifies behaviour and architecture, Prettier
formats everything that is not PHP. One aggregate command runs the whole chain, and
continuous integration runs that same command and nothing else.

This supersedes the foundation spec's organising principle. That spec declined Rector,
declined a coverage threshold, declined raising static analysis, declined Pest, and
stated `Additions. None. The foundation is the kit, unmodified.` All five refusals are
reversed here, deliberately. Their motive — staying diffable against upstream — is
abandoned in favour of a gate that binds. What survives from that spec is its single
seam: one command, identical locally and in CI.

## User Stories

1. As a developer, I want a single command that runs every quality check, so that I never wonder whether my change is acceptable.
2. As a developer, I want that command to be exactly what continuous integration runs, so that a green local run cannot become a red pipeline.
3. As a developer, I want a separate single command that fixes everything fixable, so that mechanical problems never reach a review.
4. As a developer, I want the committed tree to be a fixed point of the whole chain, so that re-running the tools changes nothing.
5. As a developer, I want the tools to run in a defined order, so that two rewriters never fight over the same file.
6. As a developer, I want the cheapest checks to run first, so that a formatting mistake does not cost me a coverage run.
7. As a developer, I want strict types declared in every file, so that no silent coercion reaches production.
8. As a developer, I want loose comparisons rejected, so that type juggling is never the cause of a defect here.
9. As a developer, I want imports ordered mechanically, so that import order is never touched in a review.
10. As a developer, I want redundant commentary removed automatically, so that a docblock in this tree is worth reading.
11. As a developer, I want to be able to keep a comment that carries real explanation, so that the rule removes noise rather than knowledge.
12. As a developer, I want static analysis at its strictest expressible setting, so that type defects are caught before runtime.
13. As a developer, I want analysis to cover my tests too, so that the largest body of code in the project is not exempt.
14. As a developer, I want analysis pinned to the language version the pipeline runs, so that syntax my machine accepts cannot break the pipeline.
15. As a developer, I want the language floor declared once and respected everywhere, so that four files cannot disagree about which version this is.
16. As a developer, I want architectural rules verified by tests, so that structural drift fails visibly instead of accumulating.
17. As a developer, I want every line of application code executed by a test, so that untested code cannot be committed.
18. As a developer, I want business logic kept out of route files, so that the measured perimeter cannot be bypassed.
19. As a developer, I want a way to check whether my tests actually detect changes, so that coverage does not become a number I trust blindly.
20. As a developer, I want my templates, styles, scripts, workflows and documentation formatted mechanically, so that the frontend is held to the same standard as the backend.
21. As a developer, I want Tailwind classes sorted automatically in my templates, so that class order is never a matter of opinion.
22. As a maintainer, I want each tool to own its concern alone, so that the chain converges instead of oscillating.
23. As a maintainer, I want the fixers and the verifiers separated, so that a rule that fails is documented by its failure rather than repaired in silence.
24. As a maintainer, I want machine-owned files left to their owners, so that a dependency install does not turn the pipeline red.
25. As a maintainer, I want tool thresholds pinned rather than aliased, so that a tool upgrade cannot change the bar without a commit.
26. As a maintainer, I want no suppression mechanism available, so that a failing check is fixed rather than filed away.
27. As a maintainer, I want the tools pinned in a lockfile, so that the chain is reproducible.
28. As an agent working in this repository, I want the standard I am held to expressed as executable configuration, so that I can verify my own work before reporting it done.
29. As an agent working in this repository, I want to be told what I may not do when a check fails, so that I fix the code instead of weakening the gate.
30. As a future contributor, I want the departure from the upstream kit recorded as a decision, so that I do not read it as neglect.
31. As a future contributor, I want the replacement of the test runner recorded as a decision, so that I do not treat the reversal as an accident.

## Implementation Decisions

**Scope of enforcement.** Everything, without exception, including the files the kit
shipped. Measured: `declare_strict_types` alone touches 26 of the 30 PHP files. Two
narrower positions were considered and rejected. Restricting strictness to `app/` is not
mechanically expressible — `pint.json` carries a single rule set, and excluding a path
removes it from the linter entirely rather than relaxing it, so `config/` would end up
with no formatting at all. Restricting only the coverage requirement was rejected because
the boundary would become the gap agents fall through, in the area where mistakes are
most expensive. The accepted cost: the tree is no longer byte-identical to the published
kit, so a kit upgrade becomes a manual merge rather than a diff, and "rebuild" now reads
"reinstall the kit, then replay the chain".

**Ownership.** Exactly one fixer per concern, plus an independent verifier wherever one
exists. Verifiers never fix. Two fixers on one concern can oscillate, which would make
the aggregate command non-terminating — a failure mode that is silent and unpleasant to
diagnose.

| Concern | Fixer | Verifier |
| --- | --- | --- |
| `declare(strict_types=1)` | Pint | Pest arch |
| Strict equality | Pint | Pest arch |
| Import order, fully-qualified names | Pint | — |
| Docblock and comment hygiene | Pint | — |
| `final` | Rector | Pest arch |
| Dead code, type declarations | Rector | Larastan |
| Blade, CSS, JS, YAML, Markdown | Prettier | — |
| `composer.json`, `package.json`, lockfiles | Composer, npm | — |

`final` belongs to Rector and explicitly not to Pint. This is technical, not a matter of
taste: Pint's `final_class` is purely syntactic and would finalise `Tests\TestCase`,
which exists to be extended. `FinalizeClassesWithoutChildrenRector` performs inheritance
analysis first. Only one of the two is correct.

**Chain order.** Fixing runs Rector, then Pint, then Prettier — Rector emits code in its
own formatting and Pint normalises afterwards. Checking has no such constraint: each tool
observes a frozen tree independently, so the check chain is ordered by increasing cost.

**Language floor.** Raised to PHP `^8.5`, from `^8.3`. This closes a real gap: the
constraint said 8.3, CI installed 8.3, and the development machine runs 8.5.8, so 8.4
syntax would pass locally and fail in CI. After the change, `composer.json`, the workflow,
Larastan's `phpVersion` and Rector's target all say 8.5. Verified: Rector ships
`SetList::PHP_85`, `shivammathur/setup-php` supports `8.5`, and `laravel/framework`
v13.22 requires `php ^8.3`.

**Pint.** Preset `laravel`, plus additive rules. Swapping presets was measured and
rejected — `psr12` (5 files), `per` (13) and `symfony` (20) are not stricter than
`laravel`, they disagree with it: `symfony` imposes snake_case test methods and spaced
concatenation, and PSR-12 is a floor that the Laravel preset already exceeds. A swap would
buy churn instead of rigour. Rules added: `declare_strict_types`, `strict_comparison`,
`strict_param`, `ordered_imports` (alpha), `global_namespace_import` limited to classes,
`ordered_class_elements`, `no_superfluous_phpdoc_tags`,
`nullable_type_declaration_for_default_null_value`, `fully_qualified_strict_types`,
`no_useless_else`, `no_useless_return`, `simplified_null_return`,
`explicit_string_variable`, `heredoc_to_nowdoc`, `date_time_immutable`, and
`Pint/phpdoc_type_annotations_only`.

`final_class` and `void_return` are excluded because Rector owns those concerns.
`native_function_invocation` is excluded on a technical ground: it wants `\count()` where
`global_namespace_import` with function importing wants `use function count;`. The two are
in direct conflict, and the micro-optimisation does not justify the visual noise.

**Comments.** `Pint/phpdoc_type_annotations_only` does not merely tidy docblocks — it
forbids comments. Any block or line comment without an `@` annotation is deleted, unless
prefixed `@note`, `@warning` or `@todo`. Measured on the current tree, it removes
"Register any application services." above `register()`, the prose docblock of
`initials()`, and a commented-out import in `User.php`, while preserving `@property`,
`@return array<string, string>` and `@use HasFactory<UserFactory>`. This is the rule best
aimed at agent-authored filler, and it is the reason the escape prefixes matter: a comment
becomes a deliberate act rather than a reflex. `config/` is exempt by the rule's own
design.

**Rector.** `withPhpSets()` with no argument — it reads `composer.json` and will target
8.5 on its own. Laravel sets through `withSetProviders(LaravelSetProvider::class)` and
`withComposerBased(laravel: true)`. Prepared sets enabled: `deadCode`, `codeQuality`,
`typeDeclarations`, `privatization`, `earlyReturn`, `if`, `instanceOf`, `rectorPreset`,
`carbon`. `privatization` is a synergy rather than a conflict — it pushes `protected`
toward `private`, which is the direction the architecture preset demands.

Disabled: `codingStyle`, which overlaps Pint frontally. `phpunitCodeQuality`,
`phpunitNarrowAsserts` and `phpunitMockToStub`, which target PHPUnit test classes that
will not exist. `doctrineCodeQuality` and the Symfony sets, out of stack. `strictBooleans`,
deprecated by Rector itself. `namedArgs`, massive churn for no benefit. `naming`, because
it is the only set in the chain that rewrites *identifiers* — everything else edits syntax
or structure, and a name is where human intent is least guessable and least verifiable.
`typeDeclarationDocblocks`, because Pint owns docblock hygiene and a second docblock
writer is exactly the oscillation the ownership rule exists to prevent.

Rector's Laravel detection is automatic where Larastan's level is pinned, and this is not
an inconsistency: an alias like `max` moves when the *tool* updates, unrelated to the code;
Rector's detection moves when *the framework* is upgraded, which is precisely when
rewriting is wanted.

**Larastan.** Level `10`, pinned as an integer rather than the `max` alias, so that a
PHPStan release cannot raise the bar without a commit. Measured: level `max` already
passes with zero errors, so the raise from 7 is free today. Extended with
`phpstan/phpstan-strict-rules` and `phpstan/phpstan-deprecation-rules`, which live beyond
the maximum level and are the part that bites. `phpVersion: 80500`. Paths widened to
`tests/`, `public/` and the whole of `bootstrap/`; measured cost is one error, in the
example test that this work deletes. No baseline: the project starts at zero errors, so
there is nothing to record, and installing the mechanism would only offer agents somewhere
to file future failures.

**Test runner.** Pest 4 replaces PHPUnit 12. This is a consequence rather than a
preference: a coverage threshold and architecture tests are both required, and PHPUnit 12
provides neither — verified, `phpunit --help` exposes coverage report writers and no
minimum. Pest wraps PHPUnit rather than excluding it, reads `phpunit.xml`, and bundles the
arch and mutation plugins. Migration cost today is three files.

**Test style.** Functional exclusively — `it()` and `test()`, with `tests/Pest.php` binding
`Tests\TestCase`. No test classes in the tree. A hybrid policy was rejected outright: it
institutionalises a per-file judgement call that no linter can adjudicate, which is the
opposite of the goal. Keeping PHPUnit classes under Pest was rejected as paying the
migration without taking its benefit. The functional style also removes a conflict rather
than creating one — the strict architecture preset requires classes to be final and
non-abstract, and there are no test classes to exempt.

**Architecture presets.** `strict()`, `php()`, `security()` and `laravel()`, with no
`ignoring()` anywhere. The strict preset's source was read rather than its description; it
enforces no protected methods, no abstract classes, strict types, strict equality, final
classes, and no `sleep`/`usleep`. Its collisions with the current tree are resolved by
changing the code, not by exempting it: `App\Http\Controllers\Controller` is deleted (it is
abstract, empty, and extended by nothing), `AppServiceProvider::configureDefaults()`
becomes `private`, and `User::casts()` becomes `public` — necessarily public rather than
private, since Eloquent declares it `protected` and PHP forbids narrowing visibility on
override.

The recorded reservation: `not->toHaveProtectedMethods()` conflicts structurally with
Laravel, not with this code. The framework's extension points are protected by design, so
each override will have to be exposed as public, a visibility that misstates the intent.
Today this costs two keywords. If it becomes intolerable, the fallback is to write the
five expectations by hand minus that one — Pest presets cannot be cherry-picked, since
`ignoring()` excludes namespaces and never individual expectations. Adding exemptions
instead was rejected because it makes the drift invisible and cumulative.

**Prettier.** Three plugins: `prettier-plugin-blade`, `prettier-plugin-tailwindcss` and
`@prettier/plugin-php`. The third is not an addition — it is a declared peer dependency of
the first. This combination was chosen on measurement, not reputation. With
`@shufo/prettier-plugin-blade`, Blade is formatted but Tailwind classes are **not** sorted;
with `prettier-plugin-blade`, `text-white p-4 flex bg-red-500 items-center` becomes
`flex items-center bg-red-500 p-4 text-white`. Tailwind's plugin monopolises a Prettier API
and lists twelve plugins it has explicit workarounds for; no Blade plugin is among them.
`prettier-plugin-blade` solves it differently, by declaring `prettier-plugin-tailwindcss`
as a peer dependency. It also has the larger user base (129k monthly downloads against
88k). On the real `welcome.blade.php`, `@fonts`, `@vite` and every conditional directive
survive unchanged.

Configuration requires `singleQuote: true` — without it the plugin rewrites `__('Hi')` to
`__("Hi")`, disagreeing with Pint's Laravel preset — and `tabWidth: 4`, since the plugin
defaults to 2 where `.editorconfig` requires 4.

**Prettier's perimeter.** `*.blade.php`, `*.css`, `*.js`, `*.yml`, `*.md`, and JSON except
the manifests and lockfiles. `composer.json` and `package.json` are owned by Composer and
npm, which rewrite them on every install; the measured disagreement is a single missing
final newline, but that is exactly the kind of recurring, unrelated red that gets an entire
gate switched off. `composer.lock` and `package-lock.json` are generated and belong to their
tools. Markdown is included deliberately: `.scratch/` holds agent-authored tickets, the
surface this work exists to discipline, and the treatment was measured harmless — Prettier
does not reflow prose, it aligns tables and normalises emphasis markers.

`.prettierignore` is structural rather than incidental. `@prettier/plugin-php` registers
itself on `.php` and was measured claiming `app/User.php` in a probe. The ignore file
carries `*.php` followed by `!*.blade.php`, which was verified to reduce Prettier's claim
to Blade alone. That file is what materialises the Pint/Prettier boundary.

**Escape hatches.** None. `@codeCoverageIgnore` and `@pest-mutate-ignore` are forbidden and
the prohibition is enforced by a dedicated grep in the aggregate command, because no tool
in the chain watches for them. No PHPStan baseline, no arch `ignoring()`, no threshold
lowering. A pinned threshold with no exemption mechanism is a stronger constraint than a
perfect threshold with a per-file waiver, because the first cannot be negotiated.

**Delivery.** Nine tickets on `main`, each leaving the gate green, each extending
`composer test` rather than editing the workflow. Continuous integration already delegates
to `composer ci:check`, so only three tickets touch the workflow at all: the language
version, the coverage driver, and the Node install step. The order is imposed by the green
constraint — Rector before Larastan, because its type declarations are what make level 10
reachable; Rector before Pint, per the chain order; Pest before architecture, coverage and
mutation, which are all Pest features; and the architecture ticket carries the code changes
it forces, since separating them would mechanically produce a red commit.

## Testing Decisions

**What makes a good test here.** This spec adds no application behaviour. Its verification
is that the chain runs green on the committed tree and that the constraints it installs are
demonstrably active — not that new behaviour is asserted.

**Seam.** One, `composer test`, extended rather than replaced. It stays the command CI
executes, so a green local run and a green pipeline cannot diverge. This is the single
property inherited from the foundation spec and it is deliberately preserved.

**Coverage.** 100% line coverage of `app/`, enforced by Pest's `--min=100`, with pcov as
the driver. Widening the perimeter to `routes/` and `database/` was rejected: routes are
declarations and migrations are single-use scripts, so requiring coverage there produces
tests that assert the framework can route and migrate. The escape it leaves open — logic
hidden in a route closure, outside the measured perimeter — is closed by prohibiting the
logic rather than by measuring it.

Two facts about the cost, both established by reading the code. `User::initials()` carries
a ternary whose second branch a single test would not exercise. `AppServiceProvider`
registers a password-defaults closure whose production branch never executes under
`APP_ENV=testing`, and which is only invoked when a password validation runs at all;
reaching it requires deliberately faking production. 100% is cheap here, but it is not free.

**Route closures.** A plain test iterating `Route::getRoutes()` and asserting that no action
is a `Closure`. This was originally specified as an architecture test and that was wrong —
Pest's arch operates on classes and namespaces, and a route file declares neither, so the
file is invisible to it. Open point for implementation: `routes/console.php` ships a closure
from the kit, so either the rule targets web routes only, or that command becomes a class.

**Mutation.** Configured and available as `composer test:mutate`, deliberately **outside**
the blocking gate. It is the honest answer to the weakness of line coverage — traversing a
line is not testing it, and a test with no assertions covers perfectly — but it is also the
only check in the chain whose cost grows quadratically, as mutants multiplied by suite
runtime, with both factors growing with the project. Keeping it out of `composer test`
preserves the local-equals-CI property exactly, rather than letting CI become the weaker of
the two, which would block a developer locally while the pipeline stays green.

The consequence is stated rather than hidden: nothing enforces the mutation score. It is a
diagnostic instrument. `--min` is retained so the command self-evaluates when run, and
`@pest-mutate-ignore` remains forbidden — that prohibition rides on the grep, which is in
the gate. The accepted risk is rot: a command nothing triggers will one day fail for reasons
nobody tracked. A scheduled job was rejected because a red nobody reads erodes the
credibility of the gates that do matter, and this repository has no remote and no audience
for such a notification.

**Not verified.** The absence of a suppression mechanism is not asserted by a test; the
prohibition lives in the aggregate command's grep and in the quality-gate skill. Nothing
tests the tool configurations themselves — that would test Pint and Rector rather than this
repository.

## Out of Scope

- Application features. The repository after this work still has one route and one view.
- A scheduled or nightly workflow of any kind.
- Any suppression, baseline or waiver mechanism.
- Raising the mutation score to a blocking requirement.
- Branch or path coverage, and the Xdebug-in-CI cost it would carry.
- `CONTEXT.md`. There is no domain here, only tooling; the glossary would be corrupted by
  its first line.
- Deployment, hosting and environment configuration.
- Authentication and any UI component layer, both still deferred by the foundation spec.

## Further Notes

Every cost quoted in this document was measured on the tree rather than estimated, with one
exception, stated plainly: the noise `phpstan-strict-rules` produces on idiomatic Laravel
code could not be measured, because the package is not installed. If the result proves
unreasonable it is removed before the first commit, where removal is free.

Five further unknowns are deliberately left to implementation rather than guessed. Whether
`eachUserNamespace()` reads `autoload-dev`, which would put the necessarily-abstract
`Tests\TestCase` in conflict with the architecture preset. The mutation threshold, which is
set from a measurement. The reconciliation between Prettier's `tabWidth` and `.editorconfig`,
which must be checked file by file rather than assumed. The robustness of the Blade plugin
beyond the two files it was tried on — neither Blade plugin is a Laravel project, and this
class of tool has a deserved reputation for breaking on exotic directives. And the size of
the coverage ticket, the only one that is writing work rather than configuration.

The rule most likely to be regretted is identified in advance:
`not->toHaveProtectedMethods()`, whose conflict is with Laravel's design rather than with
this code. Its fallback is written down so that reaching for it later is a decision rather
than an improvisation.

The single most consequential decision here is the test runner, and the foundation spec had
already said why: the installer substitutes it for free once and never again. It is being
changed now, at three files, precisely because that warning was correct.

## Comments

> *This was generated by AI during a grilling session.*

Fifteen decisions were put and answered one at a time. The measurements that justify them
are recorded above rather than in the tickets, so that they stay together: the preset
comparison, the 26-file reach of `declare_strict_types`, the zero-error result at Larastan's
maximum level, the Prettier plugin bench, and the exact source of Pest's strict preset.

Two decisions reversed themselves during the session and both corrections are recorded in
place rather than silently applied: the route-closure rule, first specified as an
architecture test and infeasible as such, and mutation testing, first accepted into the
blocking gate and then removed from it.
