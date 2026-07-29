<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

/*
 * @note This is the mechanism ADR-0003 promised when it exempted Primitive templates from every
 * analyser. The exemption is bounded rather than a softening: a Primitive is a pure function of
 * its `@props` and its slots, so it names no application symbol and reaches no ambient state.
 * A component that knows only its props is portable by construction, which is the promise the
 * shadcn idiom makes in prose and this one keeps as a verified property.
 *
 * The shape is `RoutingTest`'s and `ContrastTest`'s — an invariant asserted by traversal, with
 * the reason for the traversal written where it happens — and so is the failure: the file, the
 * rule and the expression that matched are all named, because "a Primitive is impure" is not
 * something a contributor can act on.
 *
 * It arrives with the first Primitive rather than with the directory, and that is the whole
 * reason it is in this ticket and not an earlier one. A traversal over an empty or absent
 * directory is green for the wrong reason; a test that has never seen a file it could reject is
 * a placeholder wearing a guard's name. Three separate ways of being green for the wrong reason
 * are closed below, and each one had to be closed deliberately: the directory is asserted to
 * hold a Primitive, an expression that fails to compile is a failure rather than a clean file,
 * and every rule is fired at a specimen of the construct it forbids.
 */

/*
 * @note The rules are the rule itself rather than a copy of something declared elsewhere, which
 * is what keeps this out of the class of test the quality-gate spec refused — one that restates
 * a configuration file so the two can drift. ADR-0003 writes the rule in prose; these are the
 * same words as expressions.
 *
 * Each rule carries its own specimen, and they are one table rather than two so that a rule
 * cannot be added without one. That is not ceremony. The first draft of the facade rule excluded
 * a leading backslash from its lookbehind, to spare a fully-qualified application class being
 * reported twice; the effect was that `\Illuminate\Support\Facades\Auth::user()` — the ordinary
 * way to reach a facade from a template — matched nothing at all, and the guard was green over
 * the exact construct it exists to forbid. Duplicate reporting is harmless; a rule that cannot
 * fire is not. The specimens are written in the qualified form for that reason.
 *
 * Facades are caught by forbidding *any* static call on a class name, which is a superset:
 * `Auth::user()` and `Str::of()` are equally out of bounds, and a Primitive has no business
 * calling either. The translation helper appears nowhere below, which is how it is permitted —
 * deliberately, so that a consuming project can introduce translation without reopening the
 * rule. Nothing in this repository uses it.
 *
 * The lookbehinds exist so that a method call is not read as a helper call: `$attributes
 * ->config()` is not `config()`, and the day a Primitive legitimately has such a method the
 * guard must not cry.
 */

/**
 * @return array<string, array{pattern: non-empty-string, specimen: non-empty-string}>
 */
function rulesForPrimitives(): array
{
    return [
        'the application namespace' => [
            'pattern' => '/(?<![\w\\\])\\\?App\\\/',
            'specimen' => '{{ \App\Models\User::query() }}',
        ],
        'a static call on a class, which is how a facade reads' => [
            'pattern' => '/(?<![\w$>])[A-Z]\w*::/',
            'specimen' => '{{ \Illuminate\Support\Facades\Auth::user() }}',
        ],
        'the authentication helper' => [
            'pattern' => '/(?<![\w$>])auth\s*\(/',
            'specimen' => '{{ auth()->id() }}',
        ],
        'the request helper' => [
            'pattern' => '/(?<![\w$>])request\s*\(/',
            'specimen' => '{{ request()->path() }}',
        ],
        'the session helper' => [
            'pattern' => '/(?<![\w$>])session\s*\(/',
            'specimen' => "{{ session('key') }}",
        ],
        'the configuration helper' => [
            'pattern' => '/(?<![\w$>])config\s*\(/',
            'specimen' => "{{ config('app.name') }}",
        ],
        'the route helper' => [
            'pattern' => '/(?<![\w$>])route\s*\(/',
            'specimen' => "{{ route('home') }}",
        ],
        'the old-input helper' => [
            'pattern' => '/(?<![\w$>])old\s*\(/',
            'specimen' => "{{ old('field') }}",
        ],
    ];
}

/*
 * @note Every file is matched against every rule before anything is asserted, so one run reports
 * the whole of what is wrong rather than the first thing it met.
 *
 * Templates are filtered by extension before the directory is asserted non-empty, so that the
 * assertion says "there is a Primitive here" rather than "there is a file here" — a `.gitkeep`
 * would otherwise satisfy it and the traversal would go back to proving nothing.
 */
it('keeps every Primitive a pure function of its props and slots', function (): void {
    $primitives = array_filter(
        File::allFiles(resource_path('views/components/ui')),
        static fn (SplFileInfo $file): bool => str_ends_with($file->getFilename(), '.blade.php'),
    );

    expect($primitives)->not->toBeEmpty();

    $violations = [];

    foreach ($primitives as $primitive) {
        $template = File::get($primitive->getPathname());

        foreach (rulesForPrimitives() as $description => $rule) {
            if (preg_match($rule['pattern'], $template) === 1) {
                $violations[] = sprintf('%s reaches %s, matched by %s', $primitive->getFilename(), $description, $rule['pattern']);
            }
        }
    }

    expect($violations)->toBe([]);
});

/*
 * @note Without this the guard above is unfalsifiable. It reports what its rules matched, and a
 * rule that matches nothing — mis-escaped, or narrowed by a lookbehind reaching further than
 * intended — reads identically to a directory of clean Primitives. Firing each rule at a specimen
 * of the construct it names is what separates the two, and it is not the implementation restated:
 * a specimen is an example of forbidden Blade, written independently of the expression, so the
 * two can only agree by both being right.
 *
 * `preg_match` returning `false` is folded in here rather than asserted separately, because an
 * expression that does not compile matches nothing — and nothing is exactly what a clean
 * Primitive also produces, which is the third way this file could have gone quiet.
 */
it('fires every rule at a specimen of the construct it forbids', function (): void {
    $inert = [];

    foreach (rulesForPrimitives() as $description => $rule) {
        if (preg_match($rule['pattern'], $rule['specimen']) !== 1) {
            $inert[] = sprintf('%s is not matched by %s', $description, $rule['pattern']);
        }
    }

    expect($inert)->toBe([]);
});
