<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Component;
use Pest\Preset;

/*
 * @note The presets are invoked by constructing Pest's preset object rather than through the
 * documented `arch()->preset()` chain, and this is deliberate: do not restore the chain. It
 * resolves through a class Pest annotates with a union `@mixin`, which the analyser does not
 * follow, so at the maximum level it reports eight undefined-method errors on four correct
 * lines. Both repairs the chain would need are forbidden here — no ignore annotation, and no
 * path removed from the analysed perimeter. This form was verified equivalent rather than
 * assumed: the same sixty assertions, and a deliberately non-final class still fails. The cost
 * is that it reaches an API Pest marks internal; the mitigation is that the documented chain
 * reaches the same class by a longer route, so an upstream change breaking one breaks both.
 */

arch('strict', function (): void {
    new Preset()->strict();
});

arch('php', function (): void {
    new Preset()->php();
});

arch('security', function (): void {
    new Preset()->security();
});

arch('laravel', function (): void {
    new Preset()->laravel();
});

it('places no Livewire component at the root of its namespace', function (): void {
    /* @note The hazard is created by `livewire.view_path`, which is the views directory itself:
       a component's view mirrors its class path from the root of it, so a component sitting
       directly under the class namespace would write its view among the ordinary views. Both
       ends are read from the configuration that creates the hazard rather than written here, so
       moving either one moves this traversal with it.

       The whole tree is walked rather than its root alone, and what it found is asserted
       non-empty before anything is asserted about where it sits. Scanning the root alone would
       pass on an empty match, which is the same green a wrong `class_path` gives — this way the
       test states it reached the tree it guards before stating what is not in it. `allFiles`
       raises rather than returning nothing when the path does not exist, which is the second
       way this could have gone quiet. */
    $root = Config::string('livewire.class_path');
    $namespace = mb_rtrim(Config::string('livewire.class_namespace'), '\\');

    $components = [];

    foreach (File::allFiles($root) as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        $relative = Str::beforeLast($file->getRelativePathname(), '.php');

        $class = $namespace.'\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relative);

        if (is_subclass_of($class, Component::class)) {
            $components[$class] = $file->getRelativePath();
        }
    }

    expect($components)->not->toBeEmpty();

    expect(array_keys(array_filter($components, fn (string $directory): bool => $directory === '')))->toBe([]);
});
