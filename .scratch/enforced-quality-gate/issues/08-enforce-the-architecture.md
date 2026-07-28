# 08 — Enforce the architecture and correct what it forbids

**What to build:** structural drift starts failing the suite instead of accumulating quietly.
Classes are final, none are abstract, none expose protected methods, debugging and unsafe
functions are rejected, framework conventions are checked — and no web route action is a
closure, so business logic cannot hide outside the perimeter that the next ticket measures.

Configuration and code corrections land together, in one ticket, because separating them would
mechanically produce a red commit: the rules fail on the tree as it stands. Three collisions
are known and their resolutions already decided. An abstract, empty controller base that
nothing extends is deleted. A protected helper on the service provider becomes private. And a
model's cast declaration becomes **public** rather than private — necessarily so, because the
framework declares it protected and the language forbids narrowing visibility on override.

The route-closure rule is not an architecture assertion and cannot be one: the architecture
rules operate on classes and namespaces, and a route file declares neither, so the file is
invisible to them. It is an ordinary test reading the framework's own route registry, where a
single assertion covers every route present and future.

No exemptions anywhere. Rule sets cannot be cherry-picked — exemptions apply to namespaces,
never to individual expectations — so if the prohibition on protected methods proves
intolerable against the framework's protected extension points, the recorded fallback is to
write the individual expectations by hand minus that one. **That is a decision to raise with a
human, not to take in passing.**

**Blocked by:** 04, 06

**Status:** resolved

- [x] The four agreed rule sets are active in the suite
- [x] No exemption appears anywhere in the architecture rules
- [x] The empty abstract controller base is deleted
- [x] The service provider's protected helper is private
- [x] The model's cast declaration is public
- [x] Application classes are final wherever Rector can finalise them
- [x] A test asserts that no web route action is a closure
- [x] The treatment of the shipped console closure is decided and recorded — either the rule targets web routes only, or that command becomes a class
- [x] Whether the rules reach the test namespace is established by observation, and the finding recorded in the spec
- [x] `composer test` passes
- [x] Committed as a single commit following the repository's Conventional Commits convention

## Comments

The four presets are `strict`, `php`, `security` and `laravel`, in
`tests/Feature/ArchitectureTest.php`, and they contribute sixty assertions. No `ignoring()`
call appears anywhere in `tests/`, and none was written at any point — including as a temporary
measure while resolving the collisions.

**The invocation form departs from the documented one, and it is the only judgement call in
this ticket that was not already made by the spec.** `arch()->preset()->strict()` resolves
`preset()` through a class Pest annotates with a union `@mixin`, which PHPStan does not follow,
so the analyser reports eight errors across the four lines — an undefined method and a call on
`mixed` for each. Both obvious repairs are forbidden here: an ignore annotation, and removing
the file from the analysed perimeter. Constructing Pest's `Preset` object inside each
architecture test is neither, and it was measured equivalent rather than assumed — the same
sixty assertions, and removing `final` from the service provider still fails the suite with the
same message. What it costs is a dependency on an API Pest marks `@internal`; what makes that
acceptable is that the documented chain reaches the identical class by a longer route, so a
breaking change upstream breaks both forms alike and would be caught by the suite either way.
Raising it rather than absorbing it silently: if the maintainer would rather have the
documented form and accept nothing, the only remaining lever is the analysed perimeter, which
this repository does not offer.

**Five collisions, not the three the ticket named.** The three were resolved as decided: the
abstract `Controller` base is deleted, `AppServiceProvider::configureDefaults()` is private, and
`User::casts()` is public — public rather than private because `Model` declares it protected
and the language forbids narrowing on override. The two unnamed ones are `UserFactory` and
`DatabaseSeeder`, both non-final, and both outside `App\` — which is why the advance reading
missed them. Pest builds its perimeter from every namespace the dependency manifest declares,
and this manifest declares three.

Finalising the factory then cost one more edit, by a route worth recording. Rector's
privatisation set turns a protected member of a final class private, so `$password` became
private on the next `composer fix`; a private property is one the analyser can reason about,
and level 10 immediately reported that its `?string` type carries a `null` nothing ever
assigns. The type is now `string`. The property is still uninitialised and still assigned
through `??=`, which is `isset`-based and therefore safe on an uninitialised typed property —
verified directly rather than reasoned about. Nothing was widened or suppressed to reach this;
one tool's output became another tool's input, twice.

**"Wherever Rector can finalise them" turns out to be nowhere.** The checklist item reads as
though Rector does part of this work and the architecture rules catch the rest; on this tree it
does none of it. Its only finalisation rule targets test-case classes and the functional style
leaves no test classes, so the box is satisfied vacuously. Measured rather than inferred:
un-finalising the model and the seeder and running `rector process --dry-run` reports zero
changes. Every `final` keyword here was written by hand because the strict preset failed on its
absence. The ownership table's finality row is recorded in the spec as having a verifier and, in
practice, no fixer.

**The route assertion is scoped by the tree, not by the route file.** Thirteen routes are
registered under test and six are closures — the health endpoint, two local-storage routes,
three Livewire asset routes — all six declared inside `vendor` and none rewritable here. The
assertion resolves every closure action to its declaring file through reflection and reports
only those outside `vendor`, so it holds for route files that do not exist yet rather than for
`routes/web.php` alone. A closure whose origin cannot be resolved is reported rather than
skipped. The single route this repository declares is not a closure to begin with:
`Route::view` registers `Illuminate\Routing\ViewController`, so the assertion passes on merit.
It was verified to fail — a closure route added to `routes/web.php` is named by its URI in the
failure — and the file restored. The narrowing this buys is recorded in the spec: what the test
asserts is "no closure declared outside `vendor`", so a route file here binding a closure that a
dependency constructed would pass. That is a deliberate act rather than the inattention the rule
is aimed at, and the alternative was six unfixable failures.

**The console closure stays a closure, and the rule targets web routes only.** The alternative
was rewriting the shipped `inspire` command as a class, and it was rejected because it buys
nothing mechanical: Artisan commands are absent from the route registry, so no assertion at
this seam can reach them, and converting today's closure would not forbid tomorrow's. The gap
is recorded in the spec rather than papered over — logic placed in `routes/console.php` sits
outside both the measured perimeter and this assertion, and closing it would take a second
assertion against the console registry.

**The rules do not reach the test namespace.** This was the effort's first open unknown, and it
is settled by probe rather than by reading: a class in `Tests\` that is abstract, non-final,
carries a protected method and uses a loose comparison — four strict-preset violations at once
— passes unnoticed. Pest discards every declared namespace whose directory lies under `tests`,
so `Tests\` is outside the rules by construction and the anticipated conflict with the
necessarily-abstract base test case never arises. The consequence is that the largest body of
code in the project is analysed but not architecturally constrained, which the spec now states.
The probe was deleted.

The reserved fallback was not needed. The prohibition on protected methods cost exactly the two
keywords the spec predicted, and no individual expectation was written by hand.
