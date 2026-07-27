# 06 — Enforce the architecture and correct what it forbids

**What to build:** the four architecture presets become part of the suite, and the code that
violates them is corrected rather than exempted.

Configuration and corrections land together in one ticket because separating them would
mechanically produce a red commit — the presets fail on the tree as it stands. The `strict()`
preset's source was read rather than its documentation; it requires no protected methods, no
abstract classes, strict types, strict equality, final classes, and no `sleep`/`usleep`.

Three collisions are known and their resolutions are already decided. `User::casts()` must
become `public` rather than `private`: Eloquent declares it `protected`, and PHP forbids
narrowing visibility on override.

No `ignoring()` anywhere. Presets cannot be cherry-picked — `ignoring()` excludes namespaces,
never individual expectations — so if `not->toHaveProtectedMethods()` proves intolerable
against Laravel's protected extension points, the recorded fallback is to write the five
expectations by hand minus that one. That is a decision to raise with the user, not to take
in passing.

**Blocked by:** 05

**Status:** ready-for-agent

- [ ] An architecture test enables `strict()`, `php()`, `security()` and `laravel()`
- [ ] No `ignoring()` appears anywhere in the arch tests
- [ ] `App\Http\Controllers\Controller` is deleted — it is abstract, empty and extended by nothing
- [ ] `AppServiceProvider::configureDefaults()` is `private`
- [ ] `User::casts()` is `public`
- [ ] Application classes are `final` wherever Rector can finalise them
- [ ] A test asserts that no web route action is a `Closure`
- [ ] The treatment of the shipped closure in `routes/console.php` is decided and recorded
- [ ] Whether `eachUserNamespace()` reaches `autoload-dev`, and so `Tests\TestCase`, is established and the finding recorded in the spec
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
