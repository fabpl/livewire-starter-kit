<?php

declare(strict_types=1);

use Pest\Preset;

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
