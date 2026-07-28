<?php

declare(strict_types=1);

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
